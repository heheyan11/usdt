<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTi extends Model
{
    CONST UPDATED_AT = null;
    protected $fillable = ['amount', 'rate', 'status', 'shouxu','address'];

    CONST STATUS_WAIT = 0;
    CONST STATUS_YES = 1;
    CONST STATUS_NO = 2;

    CONST VER_WAIT = 0;
    CONST VER_YES = 1;
    CONST VER_NO = 2;

    public static $stateMap = [
        self::STATUS_WAIT => '等待审核',
        self::STATUS_YES => '已审核',
        self::STATUS_NO => '审核不通过'
    ];

    public static $verMap = [
        self::VER_WAIT => '等待验证',
        self::VER_YES => '验证通过',
        self::VER_NO => '验证失败',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
