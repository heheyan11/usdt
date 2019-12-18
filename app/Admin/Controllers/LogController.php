<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Active;
use App\Models\LogCrow;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;

class LogController extends Controller
{
    public function crow(Content $content){

        $grid = new Grid(new LogCrow());

        $grid->model()->orderByDesc('id');
      /*  $grid->filter(function ($filter) {
            $filter->expand();
        });*/
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();

        $grid->column('id', 'id');
        $grid->crow()->title('名称')->modal(function ($model) {
            $detail = $model->logforms()->get()->map(function($value){
                return $value->only(['message','amount']);
            });
            $sum = sprintf('%.4f',$detail->sum('amount'));
            $detail->push(['message'=>'<span style="color: red">合计</span>','amount'=>'<span style="color: red">'.$sum.'</span>']);
            return new Table([ '内容', '数量'], $detail->toArray());
        });

        $grid->column('amount','发放数量');
        $grid->column('send','真实发放');
        $grid->column('sub','回收数量');
        $grid->column('crowdfunding_code','计划编号');

        $grid->column('created_at', '创建时间')->display(function ($value){
            return date('Y-m-d H:i',$value);
        });


        $grid->column('crowdfunding_code','计划编号');
        return $content
            ->title('平台统计')
            ->description('平台统计')
            ->body($grid);
    }

    public function active(Content $content){

        $grid = new Grid(new Active());

        $grid->model()->orderByDesc('id');
       /* $grid->filter(function ($filter) {
            $filter->expand();
        });*/
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();
        $grid->column('id', 'id');
        $grid->column('type','类型')->display(function ($value){
            return Active::$typeMap[$value];
        });
        $grid->column('content','内容');
        $grid->column('created_at', '创建时间');
        return $content
            ->title('用户反馈')
            ->description('用户反馈')
            ->body($grid);
    }
}