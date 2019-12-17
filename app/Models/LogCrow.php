<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogCrow extends Model
{
    CONST UPDATED_AT = null;
    protected $guarded = [];
    protected $dateFormat = 'U';
    protected $appends=['date'];

    public function getDateAttribute($key)
    {
        return date('Y-m-d',$this->attributes['created_at']);
    }

    public function crow(){
        return $this->belongsTo(Crowdfunding::class,'crowdfunding_id','id');
    }

    public function logforms(){
        return $this->hasMany(LogForm::class);
    }

}
