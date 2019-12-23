<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Crow\End;
use App\Admin\Actions\Crow\Run;
use App\Admin\Actions\Crow\Send;
use App\Models\Crowdfunding;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use function foo\func;

class CrowController extends CommonCrowController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '众筹中';

    protected function getWhere(){
        return function($query){
            $query->where('status',Crowdfunding::STATUS_FUNDING);
        };
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function customGrid($grid)
    {


        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
         //   $actions->add(new Send);
          //  $actions->add(new End);
        });


    }


}
