<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UserLevel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->check_level = true;
        $this->user->save();

        $up = $this->user->ancestors;
        $start = 0;
        foreach ($up as $parent) {
            if ($start == 4) break;
            $child = \App\Models\User::query()->where('parent_id', $parent->id);
            if ($start == 0) {
                $num = $child->where('check_level', true)->count();
            } else {
                $num = $child->where('share_level', $start)->count();
            }
            if ($num >= 1) {
                $parent->share_level = ++$start;
                $parent->loglevels()->create(['message' => "{$parent->phone}满足升级条件，升为{$start}级"]);
                $parent->save();
                continue;
            }
            break;
        }
    }

    public function failed(\Exception $exception)
    {
        sendErr('用户升级队列异常');
    }
}
