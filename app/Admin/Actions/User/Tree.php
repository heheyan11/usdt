<?php

namespace App\Admin\Actions\User;

use App\Services\UserTreeService;
use Encore\Admin\Actions\RowAction;
use Encore\Admin\Layout\Content;
use Illuminate\Database\Eloquent\Model;

class Tree extends RowAction
{
    public $name = '下线树图';

    public function handle(Model $model)
    {
    }
    public function href()
    {

        return route('self.tree',['id'=>$this->getKey()]);
    }


}