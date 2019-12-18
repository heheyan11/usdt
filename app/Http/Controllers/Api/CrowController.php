<?php

namespace App\Http\Controllers\Api;
use App\Exceptions\BusException;
use App\Exceptions\InternalException;
use App\Exceptions\VerifyException;
use App\Http\Requests\BuyRequest;
use App\Http\Resources\CrowdfundingResource;
use App\Jobs\UserLevel;
use App\Models\Config;
use App\Models\Crowdfunding;
use App\Models\LogCrow;
use App\Models\OrderCancel;
use App\Models\UserCard;
use App\Models\UserCrow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class CrowController
{
    /**
     * showdoc
     * @catalog 财富计划
     * @title 列表
     * @description 财富计划列表
     * @method get
     * @url crow/index
     * @return {"code":200,"data":{"can":[{"id":1,"code":"40621","title":"\u4f17\u7b792\u53f7","target_amount":"10000.0000","total_amount":"0.0000","income":"0.0000","status":"funding","run_status":"stop","created_at":"2019-12-12 08:11:30","start_at":null,"end_at":null},{"id":2,"code":"10425","title":"\u4f17\u7b7932\u53f7","target_amount":"10000.0000","total_amount":"0.0000","income":"0.0000","status":"funding","run_status":"stop","created_at":"2019-12-12 08:24:50","start_at":null,"end_at":null}],"run":[],"stop":[]},"message":"ok"}
     * @return_param can string 可申请
     * @return_param run string 运行中
     * @return_param stop string 已停止
     * @return_param code string 订单号
     * @return_param title string 标题
     * @return_param target_amount string 目标额度
     * @return_param total_amount string 已筹到额度
     * @return_param start_at string 量化启动时间
     * @return_param end_at int 量化结束时间
     * @return_param created_at string 发布时间
     * @return_param status string 众筹状态:funding众筹中end众筹结束wait等待量化
     * @return_param run_status string 量化状态run量化中stop量化结束
     * @return_param diff_day int 倒计时天
     * @return_param income string 收益
     * @return_param loading float 众筹进度,保留2位小数
     * @remark 无
     * @number 1
     */
    public function index()
    {
        $funding = $run = $stop = [];

        $crow = Crowdfunding::all(['id', 'code', 'title', 'target_amount', 'income', 'total_amount', 'status', 'run_status', 'created_at', 'start_at', 'end_at']);

        //$user = \Auth::guard('api')->user();
        foreach ($crow as $value) {
            /* $value->isBuy = 0;
             if ($user && $value->crows()->where('user_id', $user->id)->exists()) {
                 $value->isBuy = 1;
             }*/
            $value->diff_day = 0;
            //可以众筹
            if ($value->status == Crowdfunding::STATUS_FUNDING || $value->status == Crowdfunding::STATUS_WAIT) {
                $value->loading = $value->percent;
                $funding[] = $value;
            } //量化中
            elseif ($value->run_status == Crowdfunding::RUN_START && $value->status == Crowdfunding::STATUS_END) {
                $value->created_at = Carbon::parse($value->created_at)->toDateString();
                $value->start_at = date('Y-m-d', $value->start_at);

                if ($value->end_at > time()) {
                    $cDate = Carbon::parse(date('Y-m-d', $value->end_at));
                    $value->diff_day = $cDate->diffInDays() + 1;
                }
                $run[] = $value;
            } //产品结束
            elseif ($value->run_status == Crowdfunding::RUN_STOP && $value->status == Crowdfunding::STATUS_END) {
                $value->end_at = date('Y-m-d', $value->end_at);
                $stop[] = $value;
            }
        }
        return response()->json(['code' => 200, 'data' => ['can' => $funding, 'run' => $run, 'stop' => $stop], 'message' => 'ok']);
    }
    /**
     * showdoc
     * @catalog 财富计划
     * @title 详情
     * @description 详情
     * @method get
     * @url crow/detail
     * @return {"code":200,"data":{"title":"\u4f17\u7b792\u53f7","allow":"\u64a4\u9500\u9700\u8981\u6536\u53d6\u624b\u7eed\u8d39\u767e\u5206\u4e4b10%","noallow":"\u4e0d\u80fd\u64a4\u9500\u3002","target_amount":"10000.0000","total_amount":"10000.0000","loading":100,"code":"40621","content":null,"income":"50000.0000","status":"end","run_status":"run","created_at":"2019-12-14","start_at":"2019-12-13","end_at":"2020-12-07","diff_day":356,"is_buy":1,"out":{"amount":"8000.0000","rate":"5.0000","allow_amount":"7600.0000"}},"message":"ok"}
     * @return_param title string 标题
     * @return_param allow string 允许撤销弹窗提示
     * @return_param noallow string 不允许撤销弹窗提示
     * @return_param target_amount string 仓位额度
     * @return_param total_amount string 当前申请额度
     * @return_param loading string 加载比例2位小数
     * @return_param code string 订单
     * @return_param content string 详情
     * @return_param income string 累计收益
     * @return_param start_at string 量化启动时间
     * @return_param end_at string 量化结束时间
     * @return_param created_at string 发布时间
     * @return_param status string 众筹状态:funding众筹中end众筹结束
     * @return_param run_status string 量化状态run量化中stop量化结束
     * @return_param diff_day int 倒计时天
     * @return_param is_buy int 1已购买0未购买
     * @return_param out int 如果允许撤销并且已购买，此处为撤销信息
     * @return_param out.amount int 申请额度
     * @return_param out.rate int 撤销手续费
     * @return_param out.allow_amount int 可撤销最大数量
     * @remark 无
     * @number 2
     */
    public function detail()
    {
        $id = request()->input('crow_id');
        if (!$id) throw new BusException('缺少参数', 422);

        $crow = Crowdfunding::find($id);
        return response()->json(['code' => 200, 'data' => new CrowdfundingResource($crow), 'message' => 'ok']);
    }
    /**
     * showdoc
     * @catalog 财富计划
     * @title 财富详情下部
     * @description 收益记录
     * @method get
     * @url crow/logcrow
     * @param page string 可选 当前页数
     * @param page_size string 可选 每页显示记录
     * @return {"current_page":1,"data":[{"id":5,"created_at":"1576291865","amount":"10000.0000","date":"2019-12-14"}],"first_page_url":"http:\/\/192.168.10.10\/api\/crow\/logcrow?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/crow\/logcrow?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/crow\/logcrow","per_page":4,"prev_page_url":null,"to":1,"total":1}
     * @return_param current_page string 当前页
     * @return_param current_page string 分页数据
     * @return_param last_page string 最后一页
     * @return_param per_page string 每页显示数
     * @remark 无
     * @number 3
     */
    public function logcrow()
    {
        $param = request()->input();

        $page_size = $param['page_size'] ?? 10;
        if (empty($param['crow_id'])) throw new BusException('缺少参数', 422);
        $res = LogCrow::where('crowdfunding_id', $param['crow_id'])->select('id', 'created_at', 'amount')
            ->orderByDesc('id')->paginate($page_size);
        return response()->json($res);
    }

    /**
     * showdoc
     * @catalog 财富计划
     * @title 搜索列表
     * @description 搜索返回内容
     * @method get
     * @url crow/index
     * @return {"code":200,"data":[{"title":"\u4f17\u7b792\u53f7","id":1},{"title":"\u4f17\u7b7932\u53f7","id":2}],"message":"ok"}
     * @remark 无
     * @number 4
     */
    public function search()
    {
        $title = request()->input('title');
        if (!$title) {
            return response()->json(['code' => 0, '请填写查询内容']);
        }
        $res = Crowdfunding::query()->where('title', 'like', "%$title%")->select('title', 'id')->get();
        return response()->json(['code' => 200, 'data' => $res, 'message' => 'ok']);
    }
    /**
     * showdoc
     * @catalog 财富计划
     * @title 申请财富
     * @description 购买财富计划
     * @method post
     * @url crow/buy
     * @param crow_id string 必须  申请购买计划的id
     * @param amount string 必须  购买数量
     * @param password string 必须  支付密码
     * @return {"code":200,"message":"申请成功"}
     * @remark 无
     * @number 5
     */
    public function buy(BuyRequest $request)
    {
        $param = $request->input();
        $user = \Auth::guard('api')->user();
        $isCard = UserCard::query()->where('user_id', $user->id)->exists();
        if (!$isCard) {
            throw new BusException('请去身份验证', 420);
        }
        if (!$user->paypass) {
            throw new BusException('请设置支付密码', 421);
        }
        $user->checkPassLimit($param['password'], 'pay');
        $user->load('wallet');
        if ($user->wallet->amount < $param['amount']) {
            throw new VerifyException('您的余额不足，请充值');
        }
        $conf = get_conf();
        \DB::transaction(function () use ($param, $user, $conf) {
            $crow = Crowdfunding::query()->where('id', $param['crow_id'])->lockForUpdate()->first();
            if (!$crow) {
                throw new VerifyException('计划失效');
            }
            $mycrow = UserCrow::query()->where('user_id', $user->id)->where('crowdfunding_id', $crow->id)->first();

            if ($crow->status != Crowdfunding::STATUS_FUNDING) {
                throw new VerifyException('买入失败，您申请的财富计划已满额');
            }
            $total = badd($crow->total_amount, $param['amount']);
            if ($total > $crow->target_amount) {
                throw new VerifyException('当前申请计划超额');
            }
            if ($conf['min_money'] > $param['amount']) {
                throw new VerifyException('申请数量不低于' . $conf['min_money']);
            }
            $user->wallet()->update(['amount' => bcsub($user->wallet->amount, $param['amount'])]);
            if ($mycrow) {
                $mycrow->update(['amount' => badd($mycrow->amount, $param['amount'])]);
            } else {
                $user->crows()->attach($crow, ['amount' => $param['amount']]);
            }
            $crow->total_amount = $total;
            if ($crow->status == Crowdfunding::STATUS_FUNDING && $total == $crow->target_amount) {
                $crow->status = Crowdfunding::STATUS_WAIT;
            }
            $crow->user_count++;
            $crow->save();
        });
        //累计购买数量
        if ($user->parent_id && !$user->check_level && $user->crows->sum('pivot.amount') >= $conf['force_amount']) {
            dispatch(new UserLevel($user));
        }
        return response()->json(['code' => 200, 'message' => '申请成功']);
    }

    /**
     * showdoc
     * @catalog 财富计划
     * @title 撤销计划
     * @description 撤销计划
     * @method post
     * @url crow/quit
     * @param crow_id string 必须 获取计划的id
     * @param amount string 必须  撤销数量
     * @param password string 必须  支付密码
     * @return {"code":200,"message":"申请撤销成功"}
     * @remark 无
     * @number 6
     */
    public function quit(BuyRequest $request)
    {
        $param = request()->input();
        $user = \Auth::guard('api')->user();
      //  $user->checkPassLimit($param['password'], 'pay');
        $crow = Crowdfunding::query()->where('id', $param['crow_id'])->first();
        if (!$crow) {
            throw new VerifyException('计划失效');
        }
        if ($crow->run_status == Crowdfunding::RUN_STOP) {
            throw new VerifyException('该计划已停止');
        }

        $plan = $crow->crows()->where('user_id', $user->id)->first();

        $exits = $crow->whereHas('ordercancels', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', OrderCancel::STATUS_WAIT);
        })->exists();

        if ($exits) {
            throw new VerifyException('该申请已存在，请等待审核');
        }
        if (!$plan) {
            throw new VerifyException('您没有购买该计划');
        }

        if ($param['amount'] < $crow->out_amount) {
            throw new VerifyException('最小撤销额度' . $crow->out_amount);
        }

        $shouxu = bmul($param['amount'], bdiv($crow->out_rate, 100));
        $cancelMoney = badd($param['amount'], $shouxu);
        if (bcomp($plan->amount, $cancelMoney) == -1) {
            throw new VerifyException('超出最大撤销数量');
        }
        try {
            $plan->update(['amount' => bsub($plan->amount, $cancelMoney)]);
            $crow->ordercancels()->create([
                'user_id' => $user->id,
                'amount' => $param['amount'],
                'rate' => $crow->out_rate,
                'shouxu' => $shouxu
            ]);
            return response()->json(['code' => 200, 'message' => '申请撤销成功']);
        } catch (\Exception $exception) {
            throw new VerifyException('操作失败');
        }
    }
}