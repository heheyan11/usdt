<?php


namespace App\Http\Controllers\Api;

use App\Exceptions\VerifyException;
use App\Http\Requests\CardRequest;
use App\Http\Requests\InfoRequest;
use App\Http\Resources\UserResource;
use App\Models\ChongOrder;
use App\Models\Crowdfunding;
use App\Models\LogIncome;
use App\Models\OrderTi;
use App\Models\User;
use App\Models\UserCrow;
use App\Services\CardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserController
{
    /**
     * showdoc
     * @catalog 我的
     * @title 主页
     * @description 我的信息
     * @method get
     * @url user/index
     * @return {"code":200,"data":{"id":13,"username":"\u5f20\u9f99","headimgurl":"headimg.jpg","phone":"133****6424","sex":1,"card":["\u90ed*\u7136","1****************9"],"paypass":1,"share_code":791105,"level": 0},"message":"ok"}
     * @return_param paypass int 是否设置支付密码1已设置0未设置
     * @return_param sex int 0未知1男2女
     * @return_param share_code int 分项码
     * @return_param level int 等级0到5
     * @remark 如果没有认证 card 返回空数组
     * @number 1
     */
    public function index()
    {
        $user = \Auth::guard('api')->user();
        return response()->json(['code' => 200, 'data' => new UserResource($user), 'message' => 'ok']);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 认证身份证
     * @description 认证身份提交
     * @param face string 必填 正面照片(原样返回不要加域名)
     * @param back string 必填 反面照片(不要加域名)
     * @method post
     * @url user/auth
     * @return {"code":200,"message":"\u8ba4\u8bc1\u6210\u529f","data":{"name":"\u5f20\u4e09","code":"610522151122995535"}}
     * @return_param name string 身份证姓名
     * @return_param code string 身份证号码
     * @remark 无
     * @number 1
     */
    public function auth(CardRequest $request, CardService $service)
    {
        $param = $request->input();
        $user = \Auth::guard('api')->user();

        return response()->json(['code' => 200, 'message' => '认证成功', 'data' => ['name' => '李名', 'code' => '610523199205351155']]);
        if ($user->is_verify == 1) {
            throw new VerifyException('请勿重复审核');
        }

        $face = $service->checkImg($param['face'], 'face');
        $verify = $service->checkStr($face['name'], $face['num']);
        $back = $service->checkImg($param['back'], 'back');

        $param['name'] = $verify['name'];
        $param['code'] = $verify['idNo'];
        $param['province'] = $verify['province'];
        $param['city'] = $verify['city'];
        $param['county'] = $verify['county'];
        $param['birthday'] = $verify['birthday'];
        $param['age'] = $verify['age'];

        $param['address'] = $face['address'];
        $param['nationality'] = $face['nationality'];
        $param['sex'] = $face['sex'];

        $param['issue'] = $back['issue'];
        $param['start_date'] = $back['start_date'];
        $param['end_date'] = $back['end_date'];

        $rs = $user->card()->create($param);
        $user->is_verify = 1;
        $user->save();

        if ($rs) {
            return response()->json(['code' => 200, 'message' => '认证成功', 'data' => ['name' => $verify['name'], 'code' => $verify['idNo']]]);
        }
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 修改个人信息
     * @description 修改昵称，性别，头像
     * @param username string 必填 姓名
     * @param sex string 必填 性别
     * @param headimgurl string 必填 头像(原样返回不要加域名)
     * @method post
     * @url user/changeinfo
     * @return {"code":200,"message":"\u8ba4\u8bc1\u6210\u529f","data":{"name":"\u5f20\u4e09","code":"610522151122995535"}}
     * @remark 无
     * @number 1
     */
    public function changeInfo(InfoRequest $request)
    {
        $param = $request->input();
        $len = mb_strlen($param['username'], 'utf-8');
        if ($len < 2 || $len > 5) {
            throw new VerifyException('昵称长度在2-5位');
        }
        if (!in_array($param['sex'], [0, 1, 2])) {
            throw new VerifyException('性别参数错误');
        }
        \Auth::guard('api')->user()->update(['name' => $param['username'], 'headimgurl' => $param['headimgurl'], 'sex' => $param['sex']]);
        return response()->json(['code' => 200, 'message' => '修改成功']);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 我的财富计划
     * @description 我的财富计划
     * @method get
     * @url user/crows
     * @return {"code":200,"data":{"can":[],"run":[{"id":1,"code":"40621","title":"\u4f17\u7b792\u53f7","target_amount":"10000.0000","total_amount":"10000.0000","status":"end","income":"10000.0000","run_status":"run","created_at":"2019-12-14 00:00:00","start_at":"2019-12-13","end_at":1607322304,"diff_day":357}],"stop":[]},"message":"ok"}
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
     * @return_param status string 众筹状态:funding众筹中end众筹结束
     * @return_param run_status string 量化状态run量化中stop量化结束
     * @return_param diff_day int 倒计时天
     * @return_param income string 收益
     * @return_param loading float 众筹进度,保留2位小数
     * @remark 无
     * @number 1
     */
    public function crows()
    {
        $funding = $run = $stop = [];
        $user = \Auth::guard('api')->user();

        $crow = Crowdfunding::whereHas('crows', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->select('id', 'code', 'title', 'target_amount', 'total_amount', 'status', 'income', 'run_status', 'created_at', 'start_at', 'end_at')->get();

        foreach ($crow as $value) {
            //   dd($value->toArray());die;
            $value->diff_day = 0;
            //可以众筹
            if ($value->status == Crowdfunding::STATUS_FUNDING || $value->status == Crowdfunding::STATUS_WAIT) {
                $value->loading = $value->percent;
                $funding[] = $value;
            } //量化中
            elseif ($value->run_status == Crowdfunding::RUN_START && $value->status == Crowdfunding::STATUS_END) {
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
     * @catalog 我的
     * @title 我的收益
     * @description 我的收益
     * @method get
     * @url user/income
     * @return {"code":200,"message":"ok","data":{"my":"8000.0000","friend":"2000.0000","income":"6576.3040"}}
     * @return_param my string 我申请的
     * @return_param friend string 好友申请的
     * @return_param income string 总收益
     * @return_param usdt string 比率
     * @remark 无
     * @number 1
     */
    public function income()
    {
        $user = \Auth::guard('api')->user();
        Cache::forget('user_income' . $user->id);

        $data = Cache::remember('user_income' . $user->id, 15, function () use ($user) {

            //我长在参与量化
            $my = UserCrow::whereHas('crow', function ($query) {
                $query->where('run_status', '<>', Crowdfunding::RUN_STOP)->where('status', '<>', Crowdfunding::STATUS_END);
            })->where('user_id', $user->id)->sum('amount');

            //好友正在参与量化
            $friend = UserCrow::whereHas('crow', function ($query) {
                $query->where('run_status', '<>', Crowdfunding::RUN_STOP)->where('status', '<>', Crowdfunding::STATUS_END);
            })->whereIn('user_id', $user->children()->pluck('id'))->sum('amount');

            //我的总收益
            $income = LogIncome::query()->where('user_id', $user->id)->where('is_team', LogIncome::TEAM_NO)->sum('income');
            return ['my' => $my, 'friend' => $friend, 'income' => $income];
        });
        $data['usdt'] = Cache::get('usdt');

        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $data]);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 我的收益记录
     * @description 我的收益记录
     * @method get
     * @param date 可选 string 日期2019-10
     * @url user/incomelog
     * @return {"current_page":1,"data":[{"title":"\u8d21\u732e\u5956 \u7531\uff1a1337****424\u63d0\u4f9b","amount":"2000.0000","income":"15.9040","created_at":"2019-12-17"},{"title":"\u76f4\u63a8\u597d\u53cb1337****424\u63d0\u4f9b","amount":"2000.0000","income":"198.8000","created_at":"2019-12-17"},{"title":"\u4f17\u7b792\u53f7","amount":"8000.0000","income":"6361.6000","created_at":"2019-12-17"}],"first_page_url":"http:\/\/192.168.10.10\/api\/user\/incomelog?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/user\/incomelog?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/user\/incomelog","per_page":30,"prev_page_url":null,"to":3,"total":3}
     * @return_param current_page string 当前页
     * @return_param current_page string 分页数据
     * @return_param last_page string 最后一页
     * @return_param per_page string 每页显示数
     * @return_param title string 消息
     * @return_param amount string 申请额度
     * @return_param income string 获得收益
     * @return_param created_at string 时间
     * @remark 无
     * @number 1
     */
    public function incomelog()
    {
        $param = request()->input();


        $user = \Auth::guard('api')->user();

        $page_size = $param['page_size'] ?? 30;

        $query = LogIncome::where('user_id', $user->id)->where('is_team', LogIncome::TEAM_NO);

        if (!empty($param['date'])) {
            list($y, $m, $d) = explode('-', $param['date']);
            if (!checkdate($m, $d, $y)) {
                throw new VerifyException('日期格式错误');
            }
            $whereTime = getMonthDays($param['date']);
            $query->where('created_at', '>', $whereTime['start'])->where('created_at', '<', $whereTime['end']);
        }
        $res = $query->select('title', 'amount', 'income', 'created_at')->orderByDesc('id')->paginate($page_size);


        return response()->json($res);
    }


    /**
     * showdoc
     * @catalog 我的
     * @title 团队收益
     * @description 团队收益统计
     * @method get
     * @url user/teamincome
     * @return {"code":200,"message":"ok","data":{"friend":"1590.4000","team":"214.7040"}}
     * @return_param my string 我申请的
     * @return_param friend string 好友收益
     * @return_param team string 我的团队收益总计
     * @remark 无
     * @number 1
     */
    public function teamincome()
    {
        $user = \Auth::guard('api')->user();
        $data = Cache::remember('user_teamincome' . $user->id, 15, function () use ($user) {
            $friend = LogIncome::query()->whereIn('user_id', $user->children()->pluck('id'))->where('is_team', LogIncome::TEAM_NO)->sum('income');
            $team = LogIncome::query()->where('user_id', $user->id)->where('is_team', LogIncome::TEAM_YES)->sum('income');
            return ['friend' => $friend, 'team' => $team];
        });
        return response()->json(['code' => 200, 'message' => 'ok', 'data' => $data]);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 团队收益记录
     * @description 团队收益记录
     * @method get
     * @url user/teamincomelog
     * @return {"current_page":1,"data":[{"title":"\u8d21\u732e\u5956 \u7531\uff1a1337****424\u63d0\u4f9b","amount":"2000.0000","income":"15.9040","created_at":"2019-12-17"},{"title":"\u76f4\u63a8\u597d\u53cb1337****424\u63d0\u4f9b","amount":"2000.0000","income":"198.8000","created_at":"2019-12-17"}],"first_page_url":"http:\/\/192.168.10.10\/api\/user\/teamincomelog?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/user\/teamincomelog?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/user\/teamincomelog","per_page":30,"prev_page_url":null,"to":2,"total":2}
     * @return_param current_page string 当前页
     * @return_param current_page string 分页数据
     * @return_param last_page string 最后一页
     * @return_param per_page string 每页显示数
     * @return_param title string 消息
     * @return_param amount string 申请额度
     * @return_param income string 获得收益
     * @return_param created_at string 时间
     * @remark 无
     * @number 1
     */
    public function teamincomelog()
    {
        $param = request()->input();
        $user = \Auth::guard('api')->user();
        $page_size = $param['page_size'] ?? 30;
        $res = LogIncome::where('user_id', $user->id)->where('is_team', LogIncome::TEAM_YES)->select('title', 'amount', 'income', 'created_at')->orderByDesc('id')->paginate($page_size);
        return response()->json($res);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 提币记录
     * @description 提币记录
     * @method get
     * @url user/tilog
     * @return {"current_page":1,"data":[{"amount":"500.0000","created_at":"2019-12-18 01:33:45"}],"first_page_url":"http:\/\/192.168.10.10\/api\/user\/tilog?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/user\/tilog?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/user\/tilog","per_page":30,"prev_page_url":null,"to":1,"total":1}
     * @return_param current_page string 当前页
     * @return_param current_page string 分页数据
     * @return_param last_page string 最后一页
     * @return_param per_page string 每页显示数
     * @return_param amount string 申请额度
     * @return_param created_at string 时间
     * @remark 无
     * @number 1
     */
    public function tilog()
    {
        $param = request()->input();
        $user = \Auth::guard('api')->user();
        $page_size = $param['page_size'] ?? 30;
        $res = OrderTi::where('user_id', $user->id)->select('amount', 'created_at')->orderByDesc('id')->paginate($page_size);
        return response()->json($res);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 充币记录
     * @description 充币记录
     * @method get
     * @url user/orderchong
     * @return {"current_page":1,"data":[{"amount":"500.0000","created_at":"2019-12-18 01:33:45"}],"first_page_url":"http:\/\/192.168.10.10\/api\/user\/orderchong?page=1","from":1,"last_page":1,"last_page_url":"http:\/\/192.168.10.10\/api\/user\/orderchong?page=1","next_page_url":null,"path":"http:\/\/192.168.10.10\/api\/user\/orderchong","per_page":30,"prev_page_url":null,"to":1,"total":1}
     * @return_param current_page string 当前页
     * @return_param current_page string 分页数据
     * @return_param last_page string 最后一页
     * @return_param per_page string 每页显示数
     * @return_param amount string 申请额度
     * @return_param created_at string 时间
     * @remark 无
     * @number 1
     */
    public function orderchong()
    {
        $param = request()->input();
        $user = \Auth::guard('api')->user();
        $page_size = $param['page_size'] ?? 30;
        $res = ChongOrder::query()->where('user_id', $user->id)->select('amount', 'created_at')->orderByDesc('id')->paginate($page_size);
        return response()->json($res);
    }

    /**
     * showdoc
     * @catalog 我的
     * @title 邀请好友
     * @description 邀请好友申请记录
     * @method get
     * @url user/friend
     * @return {"code":200,"data":{"join":[{"headimgurl":"headimg.jpg","phone":"139****0102","created_at":"2019-12-09 05:59:47"}],"nojoin":[{"headimgurl":"headimg.jpg","phone":"137****5739","created_at":"2019-12-09 05:59:47"},{"headimgurl":"headimg.jpg","phone":"133****9221","created_at":"2019-12-09 05:59:47"}]}}
     * @return_param headimgurl string 头像
     * @return_param phone string 手机
     * @return_param created_at string 注册时间
     * @remark 无
     * @number 1
     */
    public function friend()
    {
        $data = Cache::remember('user_friend', 15, function () {
            $user = \Auth::guard('api')->user();
            $join = $user->children()->has('crows')->select('headimgurl', 'phone', 'created_at')->get()->map(function ($value) {
                $value->phone = substr_replace($value->phone, '****', 3, 4);
                return $value;
            });
            $nojoin = $user->children()->doesntHave('crows')->select('headimgurl', 'phone', 'created_at')->get()->map(function ($value) {
                $value->phone = substr_replace($value->phone, '****', 3, 4);
                return $value;
            });

            return ['join' => $join, 'nojoin' => $nojoin];
        });
        return response()->json(['code' => 200, 'data' => $data]);
    }
}