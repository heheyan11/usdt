<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogLevel extends Model
{
    CONST  UPDATED_AT = null;

    protected $fillable = ['message'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
