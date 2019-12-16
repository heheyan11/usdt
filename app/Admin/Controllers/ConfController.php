<?php

namespace App\Admin\Controllers;

use App\Models\Config;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ConfController extends AdminController
{


    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '项目配置';
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Config);
        $form->tools(function (Form\Tools $tools) {
            // 去掉`列表`按钮
            $tools->disableList();
            // 去掉`删除`按钮
            $tools->disableDelete();
            // 去掉`查看`按钮
            $tools->disableView();
        });
        $form->footer(function ($footer) {
            // 去掉`重置`按钮
            $footer->disableReset();
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
            // 去掉`继续编辑`checkbox
             $footer->disableEditingCheck();
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        });

        $form->ignore(['after-save'])->hidden('after-save')->default(1);

        $form->decimal('min_money', '最小筹款额度')->required();
        $form->decimal('min_ti','最小提现额度')->required();
        $form->decimal('out_rate','撤仓手续费%')->required();
        $form->decimal('refund_rate','提现手续费%')->required();
        $form->decimal('force_amount','基础升级额度(累计购买计划)')->required();

        return $form;
    }
}
