<?php


namespace App\Admin\Controllers;


use App\Admin\Actions\Crow\End;
use App\Admin\Actions\Crow\Send;
use App\Models\Crowdfunding;
use Encore\Admin\Grid;

class CrowRunController extends CommonCrowController
{
    protected function getWhere()
    {
        return function($query){
            $query->where('run_status',Crowdfunding::RUN_START);
        };
    }

    protected function customGrid(Grid $grid)
    {
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            // 去掉删除
             $actions->disableDelete();
            $actions->disableEdit();

             $actions->add(new Send());
             $actions->add(new End());
        });
    }
}