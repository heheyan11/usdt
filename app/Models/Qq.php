<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qq extends Model
{

    protected $fillable = ['nickname','headimgurl','gender','province','city','year'];
    //
    public function user(){
        return $this->hasOne(User::class);
    }
}
