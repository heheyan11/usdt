<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Models\ArticleCate;
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
        $grid = new Grid(new Article());
        $grid->model()->where('article_cate_id',3);
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
        $grid->column('clicks', __('点击量'));
        $grid->column('zan', __('点赞量'));
        $grid->column('share', __('分享量'));
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
        $show->thumb('封面图片')->image();
        $show->imgs('配图')->image();
        $show->field('content', '内容')->setEscape(false);
        $show->field('clicks', '浏览量');
        $show->field('zan', '点赞数');
        $show->field('share', '分享数');
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
        $form->hidden('article_cate_id')->default(3);
        $form->text('title', '标题')->required();
        $form->multipleImage('imgs', '配图')->removable()->uniqueName();
        $form->editor('content', '内容')->required();
        $form->saved(function (Form $form) {
            if (isset($form->model()->imgs[0])) $form->model()->thumb = $form->model()->imgs[0];
            if (!$form->model()->short_content) $form->model()->short_content = $this->real_trim($form->content);
            $form->model()->save();
        });
        return $form;
    }

    function real_trim($str, $lenth = 50)
    {
        $str = htmlspecialchars_decode($str);
        $str = str_replace(['&nbsp;', '&ldquo', "\r\n", "\r\n\t"], '', $str);
        $str = strip_tags($str);
        return mb_substr($str, 0, $lenth, "utf-8") . '...';
    }
}
