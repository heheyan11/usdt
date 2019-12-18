<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancel extends Model
{
    protected $fillable = ['amount','user_id','rate','shouxu'];

    CONST STATUS_WAIT = 0;
    CONST STATUS_YES = 1;
    CONST STATUS_NO = 2;

    public static $stateMap = [
        self::STATUS_WAIT => '等待审核',
        self::STATUS_YES => '已审核',
        self::STATUS_NO => '审核不通过'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function crow(){
        return $this->belongsTo(Crowdfunding::class,'crowdfunding_id','id');
    }
}
