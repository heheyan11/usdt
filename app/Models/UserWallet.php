<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    public $timestamps = false;
    protected $fillable = ['address','kid','ostime','privatekey','mnemonic'];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
