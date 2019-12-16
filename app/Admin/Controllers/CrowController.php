<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Crow\Run;
use App\Admin\Actions\Crow\Send;
use App\Models\CrowdFunding;
use Carbon\Carbon;
use Encore\Admin\Grid\Displayers\Actions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\MessageBag;
use function foo\func;

class CrowController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '理财计划';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $grid = new Grid(new CrowdFunding);

        $grid->filter(function ($filter) {
            $filter->expand();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('code', '编号');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->equal('status', '众筹状态')->select(Crowdfunding::$statusMap);
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->equal('run_status', '量化状态')->select(Crowdfunding::$runMap);
            });
        });

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            $actions->add(new Run);
            $actions->add(new Send);
        });
        $grid->column('code', '编号');
        $grid->column('title', '标题');
        $grid->column('base_rate', '基础%');
        $grid->column('one_rate', '一代%');
        $grid->column('two_rate', '二代%');
        $grid->column('lead_rate', '贡献奖%');
        $grid->column('manage_rate', '管理费%');
        $grid->column('target_amount', '目标金额');
        $grid->column('total_amount', '当前金额');
        $grid->column('user_count', '参与用户数');

        /* $grid->column('url', '链接地址')->display(function ($value){
             if($value)return "<a href='$value' target='_blank'>点击跳转</a>";
         });
         $grid->column('start_at', '开始时间');
         $grid->column('end_at', '结束时间');*/
        $grid->column('status', '众筹状态')->display(function ($value) {
            return Crowdfunding::$statusMap[$value];
        });
        $grid->column('run_status', '运行状态')->display(function ($value) {
            return Crowdfunding::$runMap[$value];
        });
        $grid->column('created_at', '创建时间');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CrowdFunding::findOrFail($id));

        $show->field('code', '编号');
        $show->field('base_rate', '基础%');
        $show->field('one_rate', '一代%');
        $show->field('two_rate', '二代%');
        $show->field('lead_rate', '贡献奖%');
        $show->field('target_amount', '目标金额');
        $show->field('total_amount', '当前金额');
        $show->field('user_count', '参与用户数');
        $show->field('url', '链接地址');
        $show->field('start_at', '开始时间');
        $show->field('end_at', '结束时间');
        $show->field('status', '状态');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new CrowdFunding);

        $form->number('base_rate', '基础%')->default(config('dao.base'))->required();
        $form->number('one_rate', '一代%')->default(config('dao.one'))->required();
        $form->number('two_rate', '二代%')->default(config('dao.two'))->required();
        $form->number('lead_rate', '贡献值%')->default(config('dao.lead'))->required();
        $form->number('run', '运行周期(天)')->default(config('dao.run'))->required();
        $form->decimal('manage_rate', '管理费%')->default(config('dao.manage'))->required();

        $form->decimal('target_amount', '目标额度')->default(0)->required();
        $form->switch('is_cancel', '是否可以撤销')->default(1);

        $form->text('title', '标题');
        $form->text('allow', '允许撤销提示');
        $form->text('noallow', '禁止撤销提示');
        $form->url('url', '视频地址')->rules('url');
        $form->hidden('status')->default(Crowdfunding::STATUS_FUNDING);
        $form->textarea('content', '详情');

        $form->saving(function (Form $form) {
            if ($form->base_rate + $form->one_rate + $form->two_rate + $form->lead_rate != 100) {
                $error = new MessageBag([
                    'title' => '分成和相加要等100哦!',
                ]);
                return back()->with(compact('error'));
            }
        });
        return $form;
    }
}
