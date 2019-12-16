<?php

namespace App\Admin\Controllers;

use App\Models\Notice;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class NoticeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '通知公告';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notice);

      /*  $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {

            $create->text('content', '内容');
        });*/

        $grid->column('id','Id');
        $grid->column('title','标题');
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at','修改时间');

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
        $show = new Show(Notice::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', '标题');
        $show->field('content', '内容')->setEscape(false);
        $show->field('created_at', '创建时间');
        $show->field('updated_at','修改时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Notice);
        $form->text('title')->required();
        $form->editor('content', '内容')->required();
        return $form;
    }
}
