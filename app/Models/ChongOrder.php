<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChongOrder extends Model
{

    CONST UPDATED_AT = null;
    protected $fillable = ['symbol','amount','hash'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function(ChongOrder $order){
           $order->order_no = order_number();
        });
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
