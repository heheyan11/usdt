<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChongOrder extends Model
{

    CONST UPDATED_AT = null;
    protected $fillable = ['symbol','amount','hash'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
