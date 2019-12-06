<?php

namespace App\Admin\Controllers;

use App\Models\Slide;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SlideController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '幻灯列表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Slide);
        $grid->filter(function ($filter) {
            $filter->column(1/2, function ($filter) {
                $filter->equal('title', '标题')->select(Slide::$type);
            });
        });
        $grid->column('id', 'Id');
        $grid->column('title', '标题')->display(function ($query){
            return Slide::$type[$query];
        });
        $grid->column('thumb', '图片')->image('',100,80);
        $grid->column('url','链接')->display(function ($value){
            return "<a href='$value' target='_blank'>点击跳转</a>";
        });
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
        $show = new Show(Slide::findOrFail($id));

        $show->field('id', 'Id');
        $show->field('title','标题');
        $show->field('thumb', '图片');
        $show->field('url', '链接');
        $show->field('created_at','创建时间');
        $show->field('updated_at', '修改时间');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Slide);

        $form->select('title', '标题')->options(Slide::$type)->required();
        $form->image('thumb', '图片')->removable()->uniqueName();
        $form->text('url','链接');
        return $form;
    }
}
