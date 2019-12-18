<?php

namespace App\Admin\Actions\Crow;

use App\Models\Crowdfunding;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Run extends RowAction
{
    public $name = '运行计划';

    public function handle(Model $model)
    {

        if ($model->status == Crowdfunding::STATUS_WAIT) {
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