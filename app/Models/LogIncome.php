<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogIncome extends Model
{
    CONST UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $fillable = ['amount','income','title','crowdfunding_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function crow(){
        return $this->belongsTo(Crowdfunding::class,'crowdfunding_id','id');
    }
}
