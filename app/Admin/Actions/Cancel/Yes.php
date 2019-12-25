<?php


namespace App\Admin\Actions\Cancel;


use App\Models\Crowdfunding;
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
        if ($model->crow->status == Crowdfunding::STATUS_END && $model->crow->run_status == Crowdfunding::RUN_STOP) {
            return $this->response()->error('该计划已经停止,不能撤销');
        }
        if ($model->status != OrderCancel::STATUS_WAIT) {
            return $this->response()->error('不能重复审核.');
        }
        \DB::transaction(function () use ($model) {

            $model->crow->update(['status' => Crowdfunding::STATUS_FUNDING, 'total_amount' => bsub($model->crow->total_amount, badd($model->amount,$model->shouxu))]);
            UserWallet::query()->where('user_id', $model->user_id)->increment('amount', $model->amount);
            $model->status = OrderCancel::STATUS_YES;
            $model->save();
        });
        return $this->response()->success('审核通过.')->refresh();
    }
}