<?php


namespace App\Admin\Actions\Crow;


use App\Models\Crowdfunding;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class End extends RowAction
{
    public $name = '结束计划';

    public function handle(Model $model)
    {
        if ($model->status == Crowdfunding::RUN_START) {

            $model->run_status = Crowdfunding::RUN_STOP;
            $model->save();

            dispatch(new PlanEnd($model));

            return $this->response()->success('释放中...')->refresh();
        }

    }
    public function dialog()
    {
        $this->confirm('结束计划并释放收益？');
    }

}