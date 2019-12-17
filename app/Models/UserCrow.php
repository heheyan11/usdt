<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCrow extends Model
{

    CONST STATUS_RUN = 1;
    CONST STATUS_TOP = 0;
    CONST STATUS_CANCEL = 2;

    CONST UPDATED_AT = null;

    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class);
    }


}
