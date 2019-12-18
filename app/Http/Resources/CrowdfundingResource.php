<?php

namespace App\Http\Resources;

use App\Models\Crowdfunding;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class CrowdfundingResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $diff = 0;
        if ($this->end_at && $this->end_at > time()) {
            $cDate = Carbon::parse(date('Y-m-d', $this->end_at));
            $diff = $cDate->diffInDays() + 1;
        }
        $isBuy = 0;
        $out = '';

        if($this->status != Crowdfunding::STATUS_END || $this->run_status != Crowdfunding::RUN_STOP) {
            $user = \Auth::guard('api')->user();
            if ($user) {
                $usercrow = $this->crows()->where('user_id', $user->id)->first();
                if ($usercrow) {
                    $rate = get_conf()['out_rate'];
                    $isBuy = 1;
                    $out = [
                        'amount' => $usercrow->amount,
                        'rate' => $rate,
                        'allow_amount' => bsub($usercrow->amount, ($usercrow->amount * $rate / 100))
                    ];
                }
            }
        }

        return [
            'code'=>$this->code,
            'crow_id'=>$this->id,
            'title' => $this->title,
            'allow' => $this->allow,
            'noallow' => $this->noallow,
            'target_amount' => $this->target_amount,
            'total_amount' => $this->total_amount,
            'loading' => $this->percent,
            'code' => $this->code,
            'content' => $this->content,
            'income' => $this->income,
            'status' => $this->status,
            'run_status' => $this->run_status,
            'created_at' => Carbon::parse($this->created_at)->toDateString(),
            'start_at' => $this->start_at ? date('Y-m-d', $this->start_at) : '',
            'end_at' => $this->end_at ? date('Y-m-d', $this->end_at) : '',
            'diff_day' => $diff,
            'is_buy' => $isBuy,
            'out' => $out
        ];
    }
}
