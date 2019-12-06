<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crowdfunding extends Model
{
    // 定义众筹的 3 种状态
    const STATUS_FUNDING = 'funding';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    public static $statusMap = [
        self::STATUS_FUNDING => '众筹中',
        self::STATUS_SUCCESS => '众筹成功',
        self::STATUS_FAIL    => '众筹失败',
    ];

    protected $fillable = ['code', 'url','total_amount','target_amount', 'user_count', 'status', 'start_at','end_at'];

    protected $dates = ['start_at','end_at'];

    public $timestamps = false;


    protected static function boot()
    {
        parent::boot();
        static::creating(function(Crowdfunding $crowdfunding){
            do{
                $crowdfunding->code = mt_rand(1,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
            }while($crowdfunding::query()->where('code',$crowdfunding)->exists());
        });
    }

    public function getPercentAttribute()
    {
        // 已筹金额除以目标金额
        $value = $this->attributes['total_amount'] / $this->attributes['target_amount'];

        return floatval(number_format($value * 100, 2, '.', ''));
    }
}
