<?php

namespace App\Admin\Actions\Crow;

use App\Jobs\PlanRun;
use App\Models\Crowdfunding;
use App\Models\UserCrow;
use Encore\Admin\Actions\RowAction;

class Run extends RowAction
{
    public $name = '运行计划';

    public function handle(Crowdfunding $model)
    {
        if ($model->status == Crowdfunding::STATUS_WAIT) {

            if(!$model->where('id',$model->id)->has('crows')->exists()){
                return $this->response()->error('没有人员参加');
            }
            $model->start_at = time();
            $model->end_at = time() + 86400 * $model->run;
            $model->status = Crowdfunding::STATUS_END;
            $model->run_status = Crowdfunding::RUN_START;
            $model->save();

            dispatch(new PlanRun($model));
            return $this->response()->success('运行成功...')->refresh();
        }
        return $this->response()->error('该计划不能运行');
    }

    public function dialog()
    {
        $this->confirm('确定运行？');
    }

}