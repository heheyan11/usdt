<?php


namespace App\Admin\Controllers;


use App\Models\Crowdfunding;
use Encore\Admin\Grid;

class CrowEndController extends CommonCrowController
{

    protected $title = '已结束';
    protected function getWhere()
    {
        return function($query){
            $query->where('run_status',Crowdfunding::RUN_STOP)->where('status',Crowdfunding::STATUS_END);
        };
    }

    protected function customGrid(Grid $grid)
    {
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableCreateButton();
    }
}