<?php

namespace App\Jobs;

use App\Models\Crowdfunding;
use App\Models\Message;
use App\Models\UserWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlanEnd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $plan;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->plan = $model;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::transaction(function () {
            $title = $this->plan->title;
            $this->plan->crows->each(function ($value) use ($title) {
                if ($value->amount > 0) {
                    UserWallet::query()->where('user_id', $value->user_id)->increment('amount', $value->amount);
                    Message::create(['user_id' => $value->user_id, 'title' => $title, 'content' => '量化已结束,余额'.$value->amount.'已到账']);
                }
            });
            $this->plan->run_status = Crowdfunding::RUN_STOP;
            $this->plan->status = Crowdfunding::STATUS_END;
            $this->plan->save();
        });
    }

    public function failed(\Exception $exception)
    {
        app(\App\Services\SmsService::class)->sendSMSTemplate('14836549',[13379246424],['结束计划队列异常']);
    }
}
