<?php

namespace App\Jobs;

use App\Models\LogForm;
use App\Models\UserCrow;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendHong implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $plan;
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($plan, $amount)
    {
        $this->plan = $plan;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::transaction(function () {
            $this->send();
        });
    }
    public function send()
    {
        $plans = $this->plan->crows;
        $plantIncome = 0;
        $manage_rate = bdiv($this->plan->manage_rate, 100);
        $base_rate = bdiv($this->plan->base_rate, 100);
        $one_rate = bdiv($this->plan->one_rate, 100);
        $two_rate = bdiv($this->plan->two_rate, 100);
        $lead_rate = bdiv($this->plan->lead_rate, 100);
        $record = [];
        foreach ($plans as $value) {

            //我拿到的总钱数
            $baseMoney = bmul($this->amount, bdiv($value->amount, $this->plan->total_amount));

            if ($this->plan->manage_rate > 0) {
                $shouxu = bmul($baseMoney, $manage_rate);
                $baseMoney = bsub($baseMoney, $shouxu);

                $plantIncome = badd($plantIncome, $shouxu);
                $record[] = ['message' => '扣除' . $value->user->phone . '管理费', 'amount' => $shouxu];

            }
            //给自己
            $self = bmul($baseMoney, $base_rate);
            $value->user->wallet()->increment('amount', $self);
            $value->user->logincomes()->create(['title' => $this->plan->title, 'income' => $self, 'amount' => $value->amount,'crowdfunding_id'=>$this->plan->id]);
            //给上线
            $up_1 = bmul($baseMoney, $one_rate);
            if ($value->user->parent_id && $up_1) {
                $parent = $value->user->parent;
                $parent->wallet()->increment('amount', $up_1);
                $parent->logincomes()->create(['title' => '直推好友' . str_phone($value->user->phone) . '提供', 'income' => $up_1, 'amount' => $value->amount,'crowdfunding_id'=>$this->plan->id]);
                $up_2 = bmul($baseMoney, $two_rate);
                //2级上线
                if ($parent->parent_id && $up_2) {
                    $parent->parent->wallet()->increment('amount', $up_2);
                    $parent->parent->logincomes()->create(['title' => '隔代好友' . str_phone($value->user->phone) . '提供', 'income' => $up_2, 'amount' => $value->amount,'crowdfunding_id'=>$this->plan->id]);
                } elseif ($up_2) {
                    $plantIncome = badd($plantIncome, $up_2);
                    $record[] = ['message' => '用户' . $value->user->phone . '没有2级上线', 'amount' => $up_2];
                }
            } else if ($up_1) {
                $plantIncome = badd($plantIncome, $up_1);
                $record[] = ['message' => '用户' . $value->user->phone . '没有1级上线', 'amount' => $up_1];
            }

            //给贡献
            $lead = bmul($baseMoney, $lead_rate);
            $sub = 0;
            $up = $value->user->ancestors;

            $level = $value->user->share_level;
            foreach ($up as $baba) {
                if ($baba->share_level == 0 || $baba->share_level < $level) {
                    $money = bsub($lead, $sub);
                    if ($money) {
                        $plantIncome = badd($plantIncome, $money);
                        $record[] = ['message' => "{$value->user->phone}的上线{$baba->phone}不满足拿贡献奖条件", 'amount' => $money];
                    }
                    break;
                } elseif ($baba->share_level == $level) {

                    $money = bmul($lead, 0.1);
                    $baba->wallet()->increment('amount', $money);
                    $baba->logincomes()->create(['title' => '平级贡献奖 由：' . str_phone($value->user->phone) . '提供.', 'income' => $money, 'amount' => $value->amount,'crowdfunding_id'=>$this->plan->id]);
                    $plantIncome = bsub($plantIncome, $money);
                    $record[] = ['message' => "{$value->user->phone}和{$baba->phone}平级，平台支出", 'amount' => -$money];

                } elseif ($baba->share_level > $level) {

                    switch ($baba->share_level) {
                        case 1:
                            $money = bmul($lead, 0.2);
                            $sub = badd($sub, $money);
                            break;
                        case 2:
                            $money = bsub(bmul($lead, 0.4), $sub);
                            $sub = badd($sub, $money);
                            break;
                        case 3:
                            $money = bsub(bmul($lead, 0.6), $sub);
                            $sub = badd($sub, $money);
                            break;
                        case 4:
                            $money = bsub(bmul($lead, 0.8), $sub);
                            $sub = badd($sub, $money);
                            break;
                        case 5:
                            $money = bsub($lead, $sub);
                    }
                    $baba->wallet()->increment('amount', $money);
                    $baba->logincomes()->create(['title' => '贡献奖 由：' . str_phone($value->user->phone) . '提供', 'income' => $money, 'amount' => $value->amount,'crowdfunding_id'=>$this->plan->id]);
                }
                $level = $baba->share_level;
            }
            $money = bsub($lead, $sub);
            if ($money) {
                $plantIncome = badd($plantIncome, $money);
                $record[] = ['message' => "剩余贡献奖", 'amount' => $money];
            }
        }

        $logCrows = $this->plan->logcrows()->create([
            'crowdfunding_code' => $this->plan->code,
            'amount' => $this->amount,
            'sub' => $plantIncome,
            'send' => ($this->amount - $plantIncome)
        ]);
        $logCrows->logforms()->createMany($record);

        $this->plan->income = badd($this->plan->income, $this->amount);
        $this->plan->save();
    }

}
