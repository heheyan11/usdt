<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Active extends Model
{
    //
    CONST TYPE_ERROR = 0;
    CONST TYPE_USE = 1;
    CONST TYPE_UP = 2;
    CONST TYPE_OTHER = 3;

    public static $typeMap = [
        self::TYPE_ERROR => '功能异常',
        self::TYPE_USE => '体验问题',
        self::TYPE_UP => '功能建议',
        self::TYPE_OTHER => '其他问题',
    ];
    protected $fillable  = ['content','type'];
}
