<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\BusException;
use App\Exceptions\InternalException;

use App\Exceptions\VerifyException;
use App\Http\Requests\RegisterRequest;
use App\Models\Qq;
use App\Models\User;
use App\Models\Wechat;
use App\Services\SmsService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LoginController extends BasePass
{

    /**
     * showdoc
     * @catalog 登录相关
     * @title 用户登录
     * @description 用户登录的接口
     * @method post
     * @url login
     * @param phone 必选 string 手机号码
     * @param code 可选 string 手机验证码（code和password至少选择一个）
     * @param password 可选 string 密码
     * @param wechat_openid 可选 string 微信授权过来
     * @param qq_openid 可选 string qq授权过来
     * @param parent_phone 可选 string 二维码场景值
     * @return {"token_type":"Bearer","expires_in":86400,"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI5ODIxNzJlMDg4ZjJiY2UwN2VlNjk2ZmYyZmNkMTdlYzU5ZDdiNjBiYmFmM2Q3YzZiMDU3MjY0MWYxN2MyZjM4ZWZkZjI5YmNiOTExNOiIyIiwianRpIjoiYjk4MjE3MmUwODhmMmJjZTA3ZWU2OTZmZjJmY2QxN2VjNTlkN2I2MGJiYWYzZDdjNmIwNTcyNjQxZjE3YzJmMzhlZmRmMjliY2I5MTE0MmIiLCJpYXQiOjE1NzUmSrySUTy_WWYcwIYVe9lUXzUF_r0DvZkqX9bnfuQAL-GfCM5MZ8nLLywplOjVvMXxgdGfB2sU2BwaUuCoRrYbyzZ8fzKs9GjN5BZbwLrBw","refresh_token":"def502009bf149f6e5b481ea42d4244ebca8e218fd6ce0810212381327385fab2b1a7238c196fb5e2fbd2225b35addb4e6043574b6f7c603f47848718240ed9876d7f55dc1ffe792bf3cdf67c83fe21e43cfc6f77267b9b6bae953ce7dbc13f910cf1b835073cdc14d13f03f0c62869b5eb87faffed8a03af615a3dcf7f341242629ccc6df1bac17461a7739b2f19fa9fc980a9e352b699d4738b241ebb53fff55465763130155a8fe57a5426d4c40d68efc3bbbfd6767c95f3d16680864409f486caed5f9030edb49174c0db767bf0347"}
     * @return_param token_type string 认证方式
     * @return_param expires_in int 过期时间
     * @return_param access_token string token验证凭证
     * @return_param refresh_token string token刷新凭证
     * @remark code和 password 二选一
     * @number 1
     */
    public function login(RegisterRequest $request)
    {

        $param = $request->input();

        if (Cache::has('res' . $param['phone'])) {
            $time = Cache::get('res' . $param['phone']);
            if (time() - $time < 3) {
                throw new VerifyException('请您休息一下');
            }
        } else {
            Cache::put('res' . $param['phone'], time(), 1);
        }

        $pass = null;

        if (isset($param['code'])) {
            //TODO:  验证
            /* $res = app(SmsService::class)->verifycode($param['phone'],$param['code']);
             if ($res['code'] != 200) {
                 return response()->json(['code'=>413,'message'=>'验证失败']);
            }*/

            $user = User::query()->where('phone', $param['phone'])->first();
            if (!$user) {
                //不传密码认为是验证码登录
                if (!isset($param['password'])) {
                    throw new BusException('您没有注册', 425);
                    //穿密码认为是注册
                } elseif (isset($param['password']) && strlen($param['password']) < 6) {
                    throw new VerifyException('密码不少于6位');
                }
                $insert = ['phone' => $param['phone'], 'password' => bcrypt($param['password'])];

                if (isset($param['fcode'])) {
                    $res = User::query()->where('share_code', $param['fcode'])->first();
                    if (!$res) {
                        throw new VerifyException('邀请码不存在');
                    }
                    $insert['parent_id'] = $res['id'];
                }

                \DB::transaction(function () use ($insert, $param) {
                    $user = User::create($insert);
                    $guzzle = new \GuzzleHttp\Client();
                    $url = 'http://39.107.156.221/api/GenerateAddress';
                    $response = $guzzle->get($url);
                    $rs = json_decode($response->getBody()->getContents(), true);
                    if ($rs['errcode'] != 0) {
                        throw new InternalException('注册地址错误');
                    }
                    $data = $rs['data']['eth'];
                    $data['kid'] = $data['id'];
                    $user->wallet()->create($data);
                    //event(new Registered($user));
                    //如果绑定微信
                    if (isset($param['wechat_openid'])) {
                        $wechat = Wechat::query()->where('openid', $param['wechat_openid'])->first();
                        $user->qq()->associate($wechat);
                        $user->headimgurl = $wechat->headimgurl;
                        $user->save();
                    }
                    //如果绑定QQ
                    if (isset($param['qq_openid'])) {
                        $qq = Qq::query()->where('openid', $param['qq_openid'])->first();
                        $user->wechat()->associate($qq);
                        $user->headimgurl = $qq->headimgurl;
                        $user->save();
                    }
                });
            } else {
                if (isset($param['wechat_openid'])) {
                    $wechat = Wechat::query()->where('openid', $param['wechat_openid'])->first();
                    $user->wechat()->associate($wechat);
                    $user->headimgurl = $wechat->headimgurl;
                    $user->save();
                } elseif (isset($param['qq_openid'])) {
                    $qq = Qq::query()->where('openid', $param['qq_openid'])->first();
                    $user->qq()->associate($qq);
                    $user->headimgurl = $qq->headimgurl;
                    $user->save();
                } else {
                    throw new VerifyException('少参数');
                }
            }
            $pass = config('app.private_pass');
        } else {
            if (!isset($param['password'])) {
                throw new VerifyException('请输入密码');
            }
            $pass = $param['password'];
        }
        if (!$pass) {
            return response()->json(['code' => 414, 'message' => '缺少参数code或password参数']);
        }
        return $this->blogin($param['phone'], $pass);
    }

    /**
     * showdoc
     * @catalog 登录相关
     * @title 用户刷新登录
     * @description 用户刷新登录的接口
     * @method post
     * @url refresh
     * @param refresh_token 必选 string 刷新凭证
     * @return {"token_type":"Bearer","expires_in":86400,"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI5ODIxNzJlMDg4ZjJiY2UwN2VlNjk2ZmYyZmNkMTdlYzU5ZDdiNjBiYmFmM2Q3YzZiMDU3MjY0MWYxN2MyZjM4ZWZkZjI5YmNiOTExNOiIyIiwianRpIjoiYjk4MjE3MmUwODhmMmJjZTA3ZWU2OTZmZjJmY2QxN2VjNTlkN2I2MGJiYWYzZDdjNmIwNTcyNjQxZjE3YzJmMzhlZmRmMjliY2I5MTE0MmIiLCJpYXQiOjE1NzUmSrySUTy_WWYcwIYVe9lUXzUF_r0DvZkqX9bnfuQAL-GfCM5MZ8nLLywplOjVvMXxgdGfB2sU2BwaUuCoRrYbyzZ8fzKs9GjN5BZbwLrBw","refresh_token":"def502009bf149f6e5b481ea42d4244ebca8e218fd6ce0810212381327385fab2b1a7238c196fb5e2fbd2225b35addb4e6043574b6f7c603f47848718240ed9876d7f55dc1ffe792bf3cdf67c83fe21e43cfc6f77267b9b6bae953ce7dbc13f910cf1b835073cdc14d13f03f0c62869b5eb87faffed8a03af615a3dcf7f341242629ccc6df1bac17461a7739b2f19fa9fc980a9e352b699d4738b241ebb53fff55465763130155a8fe57a5426d4c40d68efc3bbbfd6767c95f3d16680864409f486caed5f9030edb49174c0db767bf0347"}
     * @return_param token_type string 认证方式
     * @return_param expires_in int 过期时间
     * @return_param access_token string token验证凭证
     * @return_param refresh_token string token刷新凭证
     * @remark 无
     * @number 2
     */
    public function refresh(Request $request)
    {
        $param = $request->validate(['refresh_token' => 'required']);
        return $this->bFresh($param['refresh_token']);
    }

    /**
     * showdoc
     * @catalog 登录相关
     * @title 退出登录
     * @description 用户退出登录的接口
     * @method get
     * @url logout
     * @return {"code":200,"message":"ok"}
     * @return_param code int 1：成功 0：失败
     * @remark 无
     * @number 3
     */

    public function logout(Request $request)
    {
        if (\Auth::guard('api')->check()) {
            \Auth::guard('api')->user()->token()->revoke();
        }
        return response()->json(['code' => 200, 'message' => 'ok']);
    }

}