<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crowdfunding extends Model
{

    CONST UPDATED_AT = null;

    // 定义众筹的 3 种状态
    CONST STATUS_FUNDING = 'funding';
    CONST STATUS_WAIT = 'wait';
    CONST STATUS_END = 'end';
    //运行状态
    CONST RUN_START = 'run';
    CONST RUN_STOP = 'stop';

    public static $statusMap = [
        self::STATUS_FUNDING => '众筹中',
        self::STATUS_WAIT => '等待开启',
        self::STATUS_END    => '众筹完毕',
    ];

    public static $runMap = [
        self::RUN_START => '量化中',
        self::RUN_STOP => '未量化',
    ];



    protected $fillable = ['code', 'url','total_amount','target_amount', 'user_count', 'status', 'start_at','end_at',
    'title','allow','noallow'
    ];

   // protected $dates = ['start_at','end_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(Crowdfunding $crowdfunding){
            do{
                $crowdfunding->code = mt_rand(1,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
            }while($crowdfunding::query()->where('code',$crowdfunding->code)->exists());
        });
    }

    public function getPercentAttribute()
    {
        // 已筹金额除以目标金额
        $value = $this->attributes['total_amount'] / $this->attributes['target_amount'];
        return floatval(number_format($value * 100, 2, '.', ''));
    }

    public function logcrows(){
        return $this->hasMany(LogCrow::class);
    }

    public function crows(){
        return $this->hasMany(UserCrow::class);
    }

    public function ordercancels(){
        return $this->hasMany(OrderCancel::class);
    }
}
