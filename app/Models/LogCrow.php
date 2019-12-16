<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogCrow extends Model
{
    CONST UPDATED_AT = null;
    protected $fillable = ['amount','crowdfunding_code','sub','send'];
    protected $dateFormat = 'U';
    protected $appends=['date'];

    public function getDateAttribute($key)
    {
        return date('Y-m-d',$this->attributes['created_at']);
    }
}
