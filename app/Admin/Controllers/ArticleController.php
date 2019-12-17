<?php

namespace App\Admin\Controllers;

use App\Models\Article;
use App\Models\ArticleCate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ArticleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '新闻内容';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Article);
        $grid->model()->orderByDesc('id');

        $grid->filter(function ($filter) {
            $filter->expand();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('title', '名称');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->where(function ($query) {
                    $cid = $this->input;
                    if (empty($cid)) return;
                    $rs = Article::where('id', $cid)->first();

                    if ($rs->parent_id == 0) {
                        $ids = ArticleCate::where('parent_id', $rs->id)->pluck('id');

                        if (!$ids->isEmpty()) {
                            $query->whereIn('article_cate_id', $ids);
                        } else {

                            $query->where('article_cate_id', $cid);
                        }
                    } else {
                        $query->where('article_cate_id', $cid);
                    }
                }, '所属分类')->select(ArticleCate::selectOptions(null, '全部'));
            });
        });
        $grid->column('id', 'id');
        $grid->column('title', '标题');
        $grid->cate()->title('分类');
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
        $show->cate('分类')->as(function ($query) {
            return $query->title;
        });
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

        $form->select('article_cate_id', '分类')->options($res)->required()->default(array_keys($res)[1]);
        $form->text('title', '标题')->required();
        $form->multipleImage('imgs', '配图')->removable()->uniqueName();
        $form->editor('content', '内容')->required();
        $form->saved(function (Form $form) {

            if (!$form->model()->thumb) $form->model()->thumb = $form->model()->imgs[0];
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
