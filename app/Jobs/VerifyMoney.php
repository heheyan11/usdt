<?php

namespace App\Jobs;

use App\Models\ChongOrder;
use App\Models\LogIncome;
use App\Models\OrderTi;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VerifyMoney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uid;
    protected $orderId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uid, $orderId)
    {
        $this->uid = $uid;
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $totalChong = ChongOrder::query()->where('user_id', $this->uid)->sum('amount');
        $shouyi = LogIncome::query()->where('user_id', $this->uid)->sum('income');
        $total = badd($totalChong, $shouyi);
        $totalTi = OrderTi::query()->where('user_id', $this->uid)->sum('amount');
        //累计提现大于 累计收益，验证不通过
        if (bcomp($totalTi, $total) == 1) {
            OrderTi::where('id', $this->orderId)->update(['verify' => OrderTi::VER_NO]);
        } else {
            OrderTi::where('id', $this->orderId)->update(['verify' => OrderTi::VER_YES]);
        }
    }

    public function failed(\Exception $exception)
    {
        sendErr('验证提币队列异常');
    }
}
