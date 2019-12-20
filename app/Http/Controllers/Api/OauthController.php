<?php


namespace App\Http\Controllers\Api;


use App\Http\Requests\WechatRequest;
use App\Models\Qq;
use App\Models\Wechat;

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
     * @remark 1: 无
     * @number 4
     */
    public function wechat(WechatRequest $request)
    {
        $param = $request->input();
        Wechat::updateOrCreate(['openid' => $param['openid']], $param);
        return response()->json(['code' => 200, 'cmd' => 'login', 'openid' => $param['openid']]);
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
     * @remark 1: 无
     * @number 4
     */
    public function qq(WechatRequest $request)
    {
        $param = $request->input();
        $param['headimgurl'] = $param['figureurl_2'];
        Qq::updateOrCreate(['openid' => $param['openid']], $param);
        return response()->json(['code' => 200, 'cmd' => 'login', 'openid' => $param['openid']]);
    }

}