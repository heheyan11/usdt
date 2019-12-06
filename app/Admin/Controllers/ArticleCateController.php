<?php

namespace App\Admin\Controllers;

use App\Models\ArticleCate;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class ArticleCateController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '新闻分类';

    public function index(Content $content)
    {

        return $content
            ->row(function (Row $row) {

                $data = ArticleCate::tree(function (Tree $tree) {
                    $tree->disableCreate();
                    $tree->branch(function ($branch) {
                        $payload = "<strong>{$branch['title']}</strong> ({$branch['id']})";
                        return $payload;
                    });
                });

                $row->column(6, $data->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action('article-cates');
                    $form->select('parent_id', '父级分类')->options(ArticleCate::selectOptions());
                    $form->text('title', trans('admin.title'))->rules('required');

                    $form->hidden('_token')->default(csrf_token());
                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return redirect()->route('article-cates.edit', ['id' => $id]);
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {

        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ArticleCate());

        $grid->id('Id');
        $grid->parent_id('Parent id');
        $grid->title('Title');

        return $grid;
    }



    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ArticleCate());
        $form->select('parent_id', '父级分类')->options(ArticleCate::selectOptions());
        //  $form->image('thumb', '主图')->removable()->uniqueName();
        $form->text('title', 'Title');

        return $form;
    }



}
