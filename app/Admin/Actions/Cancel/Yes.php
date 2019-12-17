<?php


namespace App\Admin\Actions\Cancel;


use App\Models\OrderCancel;
use App\Models\UserCrow;
use App\Models\UserWallet;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Yes extends RowAction
{
    public $name = '允许撤销';

    public function handle(Model $model)
    {
        if ($model->status != OrderCancel::STATUS_WAIT) {
            return $this->response()->error('不能重复审核.');
        }
        \DB::transaction(function () use ($model){
            UserWallet::query()->where('user_id', $model->user_id)->increment('amount',$model->amount);
            $model->status = OrderCancel::STATUS_YES;
            $model->save();
        });
        return $this->response()->success('审核通过.')->refresh();
    }
}