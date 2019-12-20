<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\User\Tree;
use App\Models\User;
use App\Services\UserTreeService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户列表';
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->filter(function ($filter) {
            $filter->expand();
            $filter->column(1 / 2, function ($filter) {
                $filter->like('phone', '电话');
            });
        });
        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();
            $actions->add(new Tree);
        });
        $grid->column('id', 'Id');
        $grid->column('phone', '电话');
        $grid->wallet()->amount('余额');
        $grid->wallet()->address('地址');
        $grid->column('name', '昵称');
        $grid->column('parent_id', '上线');
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
        $show = new Show(User::findOrFail($id));
        $show->field('id', 'Id');
        $show->field('phone', '电话');
        $show->field('name', '昵称');
        $show->field('parent_id', '上线');
        $show->field('created_at', '创建时间');

        $show->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });;

        $show->wallet('钱包信息',function ($wallet){
            $wallet->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });;
            $wallet->amount('数量');
            $wallet->address('地址');
            $wallet->privatekey('私钥');
            $wallet->mnemonic('助记词');

        });

        $show->card('身份信息',function ($card){

            $card->panel()->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });;
            $card->name('姓名');
            $card->code('身份证号');
            $card->province('省');
            $card->city('市');
            $card->county('国家');
            $card->birthday('生日');
            $card->age('年龄');
            $card->address('地址');
            $card->nationality('民族');
            $card->sex('性别');
            $card->issue('签发机关');
            $card->start_date('起始日期');
            $card->end_date('结束日');
            $card->face('正面')->image();
            $card->back('反面')->image();

        });
        return $show;
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->mobile('phone', '电话')->readonly();
        $form->text('name', '昵称');
        $form->image('headimgurl', '头像')->uniqueName()->removable();
        $form->number('parent_id', '上线');

        return $form;
    }

    public function tree(Content $content, UserTreeService $tree)
    {
        $data = $tree->getUserTree();
        $data = array_values($data->toArray());
        $data = [[
            'id' => 0,
            'name' => '根',
            'children' => $data
        ]];
        return $content
            ->header('用户树状图')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.tree', ['data' => json_encode($data)]));
    }

    public function selftree(Content $content, UserTreeService $tree)
    {
        $id = request()->input('id');

        $path = User::query()->where('id', $id)->value('path');
        $allUser = User::query()->where('id', $id)->orWhere('path', 'like', $path . $id . '-%')->get();
        $allUser[0]->parent_id = 0;
        $data = $tree->getUserTree(null, $allUser)->toJson();
        return $content
            ->header('用户树状图')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.tree', ['data' => $data]));
    }

}
