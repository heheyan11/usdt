<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTi extends Model
{
    CONST UPDATED_AT = null;

    protected $fillable = ['amount','rate','status'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
