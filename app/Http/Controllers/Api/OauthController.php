<?php


namespace App\Http\Controllers\Api;


use App\Http\Requests\WechatRequest;
use App\Models\Qq;
use App\Models\Wechat;
use Illuminate\Support\Facades\Storage;

class OauthController
{


    /**
     * showdoc
     * @catalog 登录相关
     * @title 微信信息
     * @description 微信登录
     * @method post
     * @url wechat
     * @param openid 必选
     * @param nickname 必选
     * @param sex 必选
     * @param province 必选
     * @param city 必选
     * @param country 必选
     * @param headimgurl 必选
     * @return {"code":200,"openid":"dfdsfewf3f32323232","cmd":"login"}
     * @return_param wechat_openid string 用户openid
     * @return_param cmd string 执行路径
     * @remark 1: 没有绑定手机号，请求登录接口 带上并wechat_openid参数绑定 2: 直接返回用户登录的参数
     * @number 4
     */
    public function wechat(WechatRequest $request)
    {
        $data = $request->input();
        $wechat = Wechat::with('user')->where('openid', $data['openid'])->first();

        //没有授权
        if (!$wechat) {


            Wechat::create($data);
            return response()->json([
                'code' => 200,
                'wechat_openid' => $data['openid'],
                'cmd' => route('login')
            ]);
        }
        //没有注册
        if (!$wechat->user) {
            return response()->json([
                'code' => 200,
                'wechat_openid' => $data['openid'],
                'cmd' => route('login')
            ]);
        }
        return $this->blogin($wechat->user->phone, config('app.private_pass'));

    }

    /**
     * showdoc
     * @catalog 登录相关
     * @title QQ信息
     * @description QQ信息补全
     * @method post
     * @url qq
     * @param openid 必选
     * @param nickname 必选
     * @param gender 必选
     * @param province 必选
     * @param city 必选
     * @param year 必选
     * @param figureurl_2 必选
     * @return {"code":200,"openid":"dfdsfewf3f32323232","cmd":"login"}
     * @return_param wechat_openid string 用户openid
     * @return_param cmd string 执行路径
     * @remark 1: 没有绑定手机号，请求登录接口 带上并qq_openid参数绑定 2: 直接返回用户登录的参数
     * @number 4
     */
    public function qq(WechatRequest $request)
    {
        $data = $request->input();
        $data['headimgurl'] = $data['figureurl_2'];
        $qq = Qq::with('user')->where('openid', $data['openid'])->first();
        //没有授权
        if (!$qq) {

            Qq::create($data);
            return response()->json([
                'code' => 200,
                'qq_openid' => $data['openid'],
                'cmd' => route('login')
            ]);
        }
        //没有注册
        if (!$qq->user) {
            return response()->json([
                'code' => 200,
                'qq_openid' => $data['openid'],
                'cmd' => route('login')
            ]);
        }
        return $this->blogin($qq->user->phone, config('app.private_pass'));
    }
}