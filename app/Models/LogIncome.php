<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogIncome extends Model
{
    CONST UPDATED_AT = null;
    CONST TEAM_NO = 0;
    CONST TEAM_YES = 1;
    protected $dateFormat = 'U';
    protected $guarded = [];



    public function getCreatedAtAttribute($key)
    {
        return date('Y-m-d',$key);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function crow(){
        return $this->belongsTo(Crowdfunding::class,'crowdfunding_id','id');
    }
}
