<?php


namespace App\Admin\Actions\Cancel;


use App\Models\Message;
use App\Models\OrderCancel;
use App\Models\UserCrow;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class No extends RowAction
{
    public $name = '禁止撤销';

    public function handle(Model $model, Request $request)
    {
        $countent = $request->get('content');
        if ($model->status != OrderCancel::STATUS_WAIT) {
            return $this->response()->error('不能重复审核.');
        }
        $mycrow = UserCrow::query()->where('user_id', $model->user_id)->where('crowdfunding_id', $model->crowdfunding_id)->first();

        Message::create(['user_id' => $model->user_id, 'title' => $mycrow->crow->title, 'content' => '计划撤销失败:' . $countent]);

        $mycrow->update(['amount' => badd($mycrow->amount, badd($model->amount, $model->shouxu))]);
        $model->status = OrderCancel::STATUS_NO;
        $model->save();


        return $this->response()->success('禁止撤销成功.')->refresh();
    }

    public function form()
    {
        $this->text('content', '驳回内容展示给用户');
    }

}