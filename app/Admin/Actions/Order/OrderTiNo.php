<?php

namespace App\Admin\Actions\Order;

use App\Models\OrderTi;
use App\Models\UserWallet;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class OrderTiNo extends RowAction
{
    public $name='审核不通过';


    public function handle(Model $model)
    {
        if($model->status!=OrderTi::STATUS_WAIT){
            return $this->response()->error('不能重复审核.');
        }

        $amount = badd($model->amount,$model->shouxu);
        UserWallet::query()->where('user_id',$model->user_id)->increment('amount',$amount);
        $model->status = OrderTi::STATUS_NO;
        $model->save();

        return $this->response()->success('审核不通过.')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定审核不通过？');
    }
}