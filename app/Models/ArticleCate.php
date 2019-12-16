<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class ArticleCate extends Model
{
    use ModelTree,AdminBuilder;
    protected $fillable = ['name','order'];
    protected $casts = [
        'is_directory' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ArticleCate::class);
    }

    public function children()
    {
        return $this->hasMany(ArticleCate::class, 'parent_id');
    }

}