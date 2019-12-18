<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCrow extends Model
{

    CONST UPDATED_AT = null;

    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function crow(){
        return $this->belongsTo(Crowdfunding::class,'crowdfunding_id','id');
    }

}
