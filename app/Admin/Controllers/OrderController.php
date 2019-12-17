<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\ChongOrder;
use App\Models\LogIncome;
use App\Models\LogLevel;
use App\Models\OrderCancel;
use App\Models\OrderTi;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class OrderController extends Controller
{
    public function ti(Content $content)
    {

        $grid = new Grid(new OrderTi());

        $grid->model()->orderByDesc('id');
        $grid->filter(function ($filter) {
            $filter->expand();
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();
            // 去掉查看
            $actions->disableView();
            $actions->add(new \App\Admin\Actions\Order\OrderTiYes());
            $actions->add(new \App\Admin\Actions\Order\OrderTiNo());
        });

        $grid->column('id', 'id');
        $grid->user()->phone('用户');
        $grid->column('amount', '数量');
        $grid->column('rate', '手续费%');
        $grid->column('shouxu', '手续费');
        $grid->column('status','状态')->using(OrderTi::$stateMap)->label(['danger','success','primary']);

        $grid->column('created_at', '创建时间');

        return $content
            ->title('提币记录')
            ->description('提币记录')
            ->body($grid);
    }

    public function chong(Content $content){

        $grid = new Grid(new ChongOrder());
        $grid->model()->orderByDesc('id');
        $grid->filter(function ($filter) {
            $filter->expand();
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();

        $grid->column('id', 'id');
        $grid->user()->phone('用户');

        $grid->column('order_no', '订单号');
        $grid->column('symbol', '类型');
        $grid->column('amount', '数量');

        $grid->column('created_at', '充币时间');

        return $content
            ->title('充币记录')
            ->description('充币记录')
            ->body($grid);

    }

    public function income(Content $content){

        $grid = new Grid(new LogIncome());
        $grid->model()->orderByDesc('id');
        $grid->filter(function ($filter) {
            $filter->expand();
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();

        $grid->column('id', 'id');
        $grid->user()->phone('用户');

        $grid->crow()->code('所属计划');
        $grid->column('amount', '投入数量');
        $grid->column('income', '收入');
        $grid->column('title', '类型');

        $grid->column('created_at', '充币时间');

        return $content
            ->title('收入记录')
            ->description('收入记录')
            ->body($grid);

    }

    public function level(Content $content){
        $grid = new Grid(new LogLevel());
        $grid->model()->orderByDesc('id');
        $grid->filter(function ($filter) {
            $filter->expand();
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableActions();

        $grid->column('id', 'id');
        $grid->user()->phone('用户');

        $grid->column('message', '类型');

        $grid->column('created_at', '充币时间');

        return $content
            ->title('收入记录')
            ->description('收入记录')
            ->body($grid);
    }

    public function cancel(Content $content){
        $grid = new Grid(new OrderCancel());
        $grid->model()->orderByDesc('id');
        $grid->filter(function ($filter) {
            $filter->expand();
        });
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            // 去掉编辑
            $actions->disableEdit();
            // 去掉查看
            $actions->disableView();
            $actions->add(new \App\Admin\Actions\Cancel\Yes());
            $actions->add(new \App\Admin\Actions\Cancel\No());
        });

        $grid->column('id', 'id');
        $grid->user()->phone('用户');
        $grid->column('amount', '申请撤销数量');
        $grid->column('rate', '手续费%');
        $grid->column('shouxu', '手续费');
        $grid->column('status','状态')->using(OrderTi::$stateMap)->label(['danger','success','primary']);

        $grid->column('created_at', '创建时间');

        return $content
            ->title('撤销记录')
            ->description('撤销记录')
            ->body($grid);
    }
}