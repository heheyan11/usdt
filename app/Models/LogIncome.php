<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogIncome extends Model
{
    CONST UPDATED_AT = null;
    protected $dateFormat = 'U';
    protected $fillable = ['amount','income','title'];
}
