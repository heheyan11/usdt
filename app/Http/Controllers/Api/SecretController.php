<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BusException;
use App\Exceptions\ErrorException;
use App\Exceptions\InternalException;
use App\Exceptions\VerifyException;
use App\Http\Requests\CommonRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SetpassRequest;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;


class SecretController
{

    /**
     * showdoc
     * @catalog 工具
     * @title 发送验证码
     * @description 获取手机短信验证码
     * @method get
     * @url sms
     * @param phone 必填 string 手机号
     * @return {"code":200,"message":'短信发送成功'}
     * @return_param code string 200：发送成功； 412：发送失败
     * @remark 无
     * @number 1
     */
    public function code(RegisterRequest $request)
    {
        $phone = $request->input('phone');
       // $res = app(SmsService::class)->sendSmsCode($phone, '14835598');
      /*  if ($res['code'] != 200) {
            throw new BusException('短信发送失败', 412);
        }*/
        return response()->json(['code' => 200, 'message' => 'ok']);
    }


    /**
     * showdoc
     * @catalog 工具
     * @title 验证短信码
     * @description 短信码验证
     * @method post
     * @url smscheck
     * @param phone 必填 string 手机号
     * @param code 必填 string 验证码
     * @return {"code":200,"message":'短信验证成功'}
     * @return_param code string 200：验证成功； 413：验证失败
     * @remark 无
     * @number 1
     */
    public function smscheck(RegisterRequest $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        /*$res = app(SmsService::class)->verifycode($phone, $code);
        if ($res['code'] != 200) {
            throw new BusException('短信验证失败', 413);
        } else {*/
            return response()->json(['code' => 200, 'message' => '短信验证成功']);
        //}
    }

    /**
     * showdoc
     * @catalog 安全
     * @title 验证登录密码
     * @description 验证密码是否正确
     * @method post
     * @url secret/checkpass
     * @param password 必填 string 用户密码
     * @return {"code":200,"message":'密码验证成功'}
     * @return_param code string 200：密码正确； 302：密码错误
     * @remark 此接口尽量不要使用，把新就密码验证放在一起提交
     * @number 5
     */
    public function checkpass()
    {
        $pass = request()->input('password');
        if (!$pass) {
            throw new ErrorException('请提交原登录密码');
        }
        $password = \Auth::guard('api')->user()->value('password');
        $status = Hash::check($pass, $password);
        if ($status) {
            return response()->json(['code' => 200, 'message' => '密码正确']);
        } else {
            throw new VerifyException('密码错误');
        }
    }

    /**
     * showdoc
     * @catalog 安全
     * @title 修改登录密码
     * @description 修改登录密码
     * @method post
     * @url secret/setpass
     * @param password 可选 string 用户密码
     * @param phone 必填 string 用户手机
     * @param code 可选 string 手机验证码
     * @return {"code":200,"message":'修改密码成功'}
     * @return_param code string 200：修改登录密码成功； 0：修改登录密码失败
     * @remark 登录前：提交phone和code,登陆后提交oldpass
     * @number 5
     */
    public function setLoginPass()
    {
        $param = request()->input();
        if (isset($param['code'])) {

            if (!isset($param['phone'])) {
                response()->json(['code' => 0, 'message' => '请输入手机号']);
            }
            $res = app(SmsService::class)->verifycode($param['phone'], $param['code']);
            if ($res['code'] != 200) {
                throw new BusException('短信验证失败', 413);
            }
        } elseif (isset($param['oldpass'])) {
            $user = \Auth::guard('api')->user();
            if (!$user) {
                throw new InternalException('Unauthenticated', 401);
            }
            if (!Hash::check($param['oldpass'], $user->password)) {
                throw new VerifyException('密码错误');
            }
        } else {
            throw new VerifyException('缺少参数');
        }

        \Auth::guard('api')->user()->update(['password' => bcrypt($param['password'])]);
        return response()->json(['code' => 200, 'message' => '修改登录密码成功']);

    }

    /**
     * showdoc
     * @catalog 安全
     * @title 修改手机号
     * @description 修改手机号
     * @method post
     * @url secret/changephone
     * @param phone 必填 string 用户手机
     * @param code 必填 string 手机验证码
     * @return {"code":200,"message":'修改密码成功'}
     * @return_param code string 200：修改登录密码成功； 0：修改登录密码失败
     * @remark 登录前：提交phone和code,登陆后提交oldpass
     * @number 5
     */
    public function changePhone(RegisterRequest $request)
    {
        $param = $request->input();
        $res = app(SmsService::class)->verifycode($param['phone'], $param['code']);
        if ($res['code'] != 200) {
            throw new BusException('短信验证失败', 413);
        }
        if (User::where('phone', $param['phone'])->exists()) {
            throw new VerifyException('该手机已存在');
        }
        $user = \Auth::guard('api')->user();
        $user->phone = $param['phone'];
        $user->save();
        return response()->json(['code' => 200, 'message' => '手机修改成功']);
    }

    /**
     * showdoc
     * @catalog 安全
     * @title 设置支付密码
     * @description 设置支付密码
     * @method post
     * @param password string 要设置的支付密码
     * @url secret/setpaypass
     * @return {"code":200,"message":'设置密码成功'}
     * @remark 一次性接口
     * @number 5
     */
    public function setPayPass(SetpassRequest $request)
    {
        $pass = $request->input('password');
        $user = \Auth::guard('api')->user();
        if ($user->paypass) {
            throw new VerifyException('您已设置支付密码');
        }
        $user->update(['paypass' => bcrypt($pass)]);
        return response()->json(['code' => 200, 'message' => '设置支付密码成功']);
    }

    /**
     *
     * @catalog 安全
     * @title 设置登录密码
     * @description 设置登录密码
     * @method post
     * @param password string 要设置的登录密码
     * @url secret/setpassword
     * @param password 必填 string 用户密码
     * @return {"code":200,"message":'设置登录密码成功'}
     * @remark 一次性接口
     * @number 5
     */
    public function setPassPass(SetpassRequest $request)
    {
        $pass = $request->input('password');
        $user = \Auth::guard('api')->user();
        if ($user->password) {
            throw new VerifyException('您已设置登录密码');
        }
        $user->update(['password' => bcrypt($pass)]);
        return response()->json(['code' => 200, 'message' => '设置登录密码成功']);
    }

    /**
     * showdoc
     * @catalog 安全
     * @title 修改支付密码
     * @description 修改支付密码
     * @method post
     * @url secret/setpass
     * @param password 必填 string 用户密码
     * @param code 必填 string 手机验证码
     * @param oldpass 必填 string 旧密码
     * @return {"code":200,"message":'修改密码成功'}
     * @return_param code string 200：修改支付密码成功； 0：修改支付密码失败
     * @remark 无
     * @number 6
     */
    public function changePayPass(RegisterRequest $request)
    {
        $param = $request->input();

        if (isset($param['code'])) {
            $user = \Auth::guard('api')->user();
            $res = app(SmsService::class)->verifycode($user->phone, $param['code']);
            if ($res['code'] != 200) {
                return response()->json(['code' => 413, 'message' => '短信验证失败']);
            }
        } elseif (isset($param['oldpass'])) {

            $user = \Auth::guard('api')->user();
            if (!Hash::check($param['oldpass'], $user->paypass)) {
                throw  new VerifyException('密码错误');
            }
        } else {
            throw new VerifyException('缺少参数');
        }

        \Auth::guard('api')->user()->update(['paypass' => bcrypt($param['password'])]);

        if (Cache::has('lockpaypass' . $user->id)) {
            Cache::forget('lockpaypass' . $user->id);
        }
        return response()->json(['code' => 200, 'message' => '修改支付密码成功']);

    }


}
