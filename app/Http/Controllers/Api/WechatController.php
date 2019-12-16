<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\InternalException;
use App\Models\Wechat;
use Illuminate\Http\Request;


class WechatController extends BasePass
{

    public $app_id = '';
    public $app_secret = '';


    /**
     * showdoc
     * @catalog 登录相关
     * @title 微信登录
     * @description 微信code换取登录
     * @method post
     * @url wechat
     * @param code 必选 string 微信临时凭证
     * @return {"code":200,"openid":"dfdsfewf3f32323232","cmd":"login"}
     * @return_param openid string 用户openid
     * @return_param cmd string 执行路径
     * @return {"token_type":"Bearer","expires_in":86400,"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImI5ODIxNzJlMDg4ZjJiY2UwN2VlNjk2ZmYyZmNkMTdlYzU5ZDdiNjBiYmFmM2Q3YzZiMDU3MjY0MWYxN2MyZjM4ZWZkZjI5YmNiOTExNOiIyIiwianRpIjoiYjk4MjE3MmUwODhmMmJjZTA3ZWU2OTZmZjJmY2QxN2VjNTlkN2I2MGJiYWYzZDdjNmIwNTcyNjQxZjE3YzJmMzhlZmRmMjliY2I5MTE0MmIiLCJpYXQiOjE1NzUmSrySUTy_WWYcwIYVe9lUXzUF_r0DvZkqX9bnfuQAL-GfCM5MZ8nLLywplOjVvMXxgdGfB2sU2BwaUuCoRrYbyzZ8fzKs9GjN5BZbwLrBw","refresh_token":"def502009bf149f6e5b481ea42d4244ebca8e218fd6ce0810212381327385fab2b1a7238c196fb5e2fbd2225b35addb4e6043574b6f7c603f47848718240ed9876d7f55dc1ffe792bf3cdf67c83fe21e43cfc6f77267b9b6bae953ce7dbc13f910cf1b835073cdc14d13f03f0c62869b5eb87faffed8a03af615a3dcf7f341242629ccc6df1bac17461a7739b2f19fa9fc980a9e352b699d4738b241ebb53fff55465763130155a8fe57a5426d4c40d68efc3bbbfd6767c95f3d16680864409f486caed5f9030edb49174c0db767bf0347"}
     * @remark 1: 没有绑定手机号，请求登录接口 带上并wechat_openid参数绑定 2: 直接返回用户登录的参数
     * @number 4
     */
    public function wechat()
    {

        $code = \request()->input('code');
        $data = $this->get_access_token($code);
        //  $data['openid'] = 'dfdsfewf3f32323232';

        $wechat = Wechat::with('user')->where('openid', $data['openid'])->first();

        //没有授权
        if (!$wechat) {
            $info = $this->get_user_info($data['access_token'], $data['openid']);
            Wechat::create($info);
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

    public function get_access_token($code)
    {

        $token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $token_data = $this->http($token_url);

        if ($token_data[0] == 200) {
            $data = json_decode($token_data[1], TRUE);
            if (isset($data['errcode'])) {
                throw new InternalException($data['errmsg']);
            }
            return $data;
        }
        throw new InternalException('微信服务器access_token错误');
    }

    public function get_user_info($access_token = '', $open_id = '')
    {
        if ($access_token && $open_id) {
            $info_url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$open_id}&lang=zh_CN";
            $info_data = $this->http($info_url);

            if ($info_data[0] == 200) {
                $data = json_decode($info_data[1], TRUE);
                if (isset($data['errcode'])) {
                    throw new InternalException($data['errmsg']);
                }
                return $data;
            }
            throw new InternalException('微信服务器userinfo错误');
        }
        return FALSE;
    }




}