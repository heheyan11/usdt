<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    CONST UPDATED_AT = null;

    protected $fillable = ['name', 'code', 'face', 'back', 'status', 'province', 'city', 'county', 'birthday', 'sex', 'age','nationality','issue','start_date','end_date','address'];

    protected $touches = ['user'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
