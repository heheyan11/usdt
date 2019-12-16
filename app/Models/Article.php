<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $casts = [
        'imgs'   => 'json',
    ];
    protected $fillable = ['title','thumb','imgs','short_content','content'];
    protected $hidden = ['updated_at'];

    public function cate(){
        return $this->belongsTo(ArticleCate::class);
    }

    public function parise(){
        return $this->hasMany(ArticleParise::class);
    }

}
