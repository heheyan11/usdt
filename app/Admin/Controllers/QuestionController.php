<?php


namespace App\Admin\Controllers;


use App\Models\Article;
use App\Models\ArticleCate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class QuestionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '常见问题';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Article);
        $grid->model()->where('article_cate_id',2);
        $grid->model()->orderByDesc('id');
        $grid->filter(function ($filter) {
            $filter->expand();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('title', '名称');
            });
        });
        $grid->column('id', 'id');
        $grid->column('title', '标题');
        $grid->column('short_content', '简介');
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
        $show = new Show(Article::findOrFail($id));

        $show->field('id', 'Id');

        $show->field('title', '标题');
        $show->field('content', '内容')->setEscape(false);
        $show->field('created_at', '创建时间');
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
        $form = new Form(new Article);
        $res = ArticleCate::selectOptions(null, null);
        $form->hidden('article_cate_id')->default(2);
        $form->text('title', '标题')->required();

        $form->editor('content', '内容')->required();

        return $form;
    }


}