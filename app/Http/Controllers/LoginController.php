<?php


namespace App\Http\Controllers;


use App\Exceptions\BusException;
use App\Exceptions\InternalException;
use App\Exceptions\VerifyException;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Wechat;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{

        public function register(RegisterRequest $request){

            $param = $request->input();


            if(Cache::has('res'.$param['phone'])){
                $time = Cache::get('res'.$param['phone']);
                if(time() - $time < 3){
                    throw new VerifyException('请您休息一下');
                }
            }else{
                Cache::put('res'.$param['phone'],time(),1);
            }

            if(!isset($param['code'])){
                throw new VerifyException('缺少参数');
            }

            /* $res = app(SmsService::class)->verifycode($param['phone'],$param['code']);
            if ($res['code'] != 200) {
                return response()->json(['code'=>413,'message'=>'验证失败']);
           }*/


            $user = User::query()->where('phone', $param['phone'])->first();
            if ($user) {
                throw  new VerifyException('该手机已注册');
            }
            //不传密码认为是验证码登录
            if (!isset($param['password'])) {
                throw new BusException('您没有注册', 425);
                //穿密码认为是注册
            } elseif (isset($param['password']) && strlen($param['password']) < 6) {
                throw new VerifyException('密码不少于6位');
            }
            $insert = ['phone' => $param['phone'], 'password' => bcrypt($param['password'])];

            if (!empty($param['fcode'])) {
                $res = User::query()->where('share_code', $param['fcode'])->first();
                if(!$res){
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
                    $user->wechat()->associate($wechat);
                    $user->headimgurl = $wechat->headimgurl;
                    $user->save();
                }
                //如果绑定QQ
                if (isset($param['qq_openid'])) {
                    $qq = Wechat::query()->where('openid', $param['qq_openid'])->first();
                    $user->wechat()->associate($qq);
                    $user->headimgurl = $wechat->headimgurl;
                    $user->save();
                }
            });

            return response()->json(['code'=>200,'message'=>'ok']);

        }
}