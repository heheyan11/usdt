<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wechat extends Model
{
    //
    protected $fillable=['openid','nickname','sex','province','city','country','headimgurl'];

    public function user(){
        return $this->hasOne(User::class);
    }
}
