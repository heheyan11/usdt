<?php

namespace App\Jobs;

use App\Models\Crowdfunding;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlanRun implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $plan;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($plan)
    {
        $this->plan = $plan;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = $this->plan->crows()->where('amount','<>',0)->pluck('user_id');
        foreach ($users as $value){
            Message::create(['user_id'=>$value,'title'=>$this->plan->title,'content'=>'量化已启动']);
        }
    }

    public function failed(Exception $exception)
    {
        app(\App\Services\SmsService::class)->sendSMSTemplate('14836549',[13379246424],['运行队列计划异常']);
    }
}