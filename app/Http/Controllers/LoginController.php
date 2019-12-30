<?php


namespace App\Http\Controllers;


use App\Exceptions\BusException;
use App\Exceptions\InternalException;
use App\Exceptions\VerifyException;
use App\Http\Requests\RegisterRequest;
use App\Models\Qq;
use App\Models\User;
use App\Models\Wechat;
use App\Services\SmsService;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{

    /**
     * showdoc
     * @catalog 工具
     * @title 发送验证码
     * @description 获取手机短信验证码
     * @method get
     * @url sms
     * @param phone 必填 string 手机号
     * @return {"code":200,"message":'短信发送成功',"is_register":1}
     * @return_param code string 200：发送成功412:发送失败
     * @return_param is_register string 200：发送成功412：发送失败
     * @remark 无
     * @number 1
     */
    public function sms(RegisterRequest $request)
    {
        $phone = $request->input('phone');

        $register = User::query()->where('phone', $phone)->exists();
        if($register){
            return response()->json(['code' => 200, 'is_register' => (int)$register, 'message' => 'ok']);
        }
      
        $res = app(SmsService::class)->sendSmsCode($phone, '14835598');

        if ($res['code'] != 200) {
            throw new BusException('短信发送失败', 412);
        }

        return response()->json(['code' => 200, 'is_register' => (int)$register, 'message' => 'ok']);
    }


    public function register(RegisterRequest $request)
    {

        $param = $request->input();


        if (!isset($param['code'])) {
            throw new VerifyException('缺少参数');
        }

        $res = app(SmsService::class)->verifycode($param['phone'], $param['code']);
        if ($res['code'] != 200) {
            return response()->json(['code' => 413, 'message' => '验证失败']);
        }
        $user = User::query()->where('phone', $param['phone'])->first();
        if ($user) {
            throw  new VerifyException('该手机已注册');
        }
        $insert = ['phone' => $param['phone']];
        if (!empty($param['fcode'])) {
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
        });

        return response()->json(['code' => 200, 'message' => 'ok']);

    }
}