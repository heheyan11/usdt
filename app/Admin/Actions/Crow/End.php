<?php


namespace App\Admin\Actions\Crow;


use App\Jobs\PlanEnd;
use App\Models\Crowdfunding;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class End extends RowAction
{
    public $name = '结束计划';

    public function handle(Model $model)
    {

        if ($model->run_status == Crowdfunding::RUN_START) {

            dispatch(new PlanEnd($model));
            return $this->response()->success('释放中...')->refresh();
        }
        return $this->response()->error('该计划不满足释放条件');
    }
    public function dialog()
    {
        $this->confirm('结束计划并释放收益？');
    }

}