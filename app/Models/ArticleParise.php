<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleParise extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id','status'];

    CONST STATUS_YES = 1;
    CONST STATUS_NO = 0;

    public function user(){
        return $this->belongsTo(User::class);
    }

}
