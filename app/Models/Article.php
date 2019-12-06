<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $casts = [
        'imgs'   => 'json',
    ];
    public function cate(){
        return $this->belongsTo(ArticleCate::class,'article_cate_id','id');
    }
}
