<?php


namespace App\Admin\Controllers;


use App\Admin\Actions\Crow\Run;
use App\Models\Crowdfunding;
use Encore\Admin\Grid;

class CrowWaitController extends CommonCrowController
{
    protected $title = '等待开启';
    protected function getWhere()
    {
        return function($query){
            $query->where('status',Crowdfunding::STATUS_WAIT);
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

            $actions->add(new Run());
            //   $actions->add(new Send);
            //  $actions->add(new End);
        });
    }
}