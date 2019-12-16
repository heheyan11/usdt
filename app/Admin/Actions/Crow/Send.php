<?php

namespace App\Admin\Actions\Crow;

use App\Jobs\SendHong;
use App\Models\Crowdfunding;
use App\Models\LogForm;
use App\Models\LogIncome;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Send extends RowAction
{
    public $name = '发放红利';

    public function handle(Model $model, Request $request)
    {
        $amount = $request->get('amount');
        if ($amount < 100) {
            return $this->response()->error('发送额度太小.');
        }

        if ($model->run_status == Crowdfunding::RUN_START && $model->status == Crowdfunding::STATUS_END) {
            dispatch(new SendHong($model, $amount));
            return $this->response()->success('后台发放中.')->refresh();
        }
        return $this->response()->error('条件不满足，请检查仓位是否满以及是否运行.');
    }

    public function form()
    {
        $this->text('amount', '发放额度')->rules('regex:/^[0-9]+(.[0-9]{1,4})?$/');
    }


}