<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancel extends Model
{
    protected $fillable = ['amount','user_id','rate'];
}
