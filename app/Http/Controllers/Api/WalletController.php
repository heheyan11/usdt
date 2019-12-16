<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\VerifyException;
use App\Http\Requests\TiRequest;
use App\Models\ChongOrder;
use App\Models\OrderTi;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class WalletController
{
    private $privateKey = ['merchantkey' => 'dhasdiuahwfiuagbvkasbdiasbkcgafbasdas'];


    /**
     * showdoc
     * @catalog 我的钱包
     * @title 钱包主页
     * @description 钱包主页
     * @method get
     * @url wallet/index
     * @return {"code":200,"data":{"amount":"1590.4000","address":"0x034C7bADb6C4D1eCbb3013421D2Ee7518DcE9eCC","refund_rate":2,"min_ti":100},"message":"ok"}
     * @return_param amount string 资产估值
     * @return_param address string 钱包地址
     * @return_param refund_rate string 提币手续费%
     * @return_param min_ti string 最小提笔数量
     * @remark 无
     * @number 1
     */
    public function index()
    {
        $data = UserWallet::query()->where('user_id', \Auth::guard('api')->user()->id)
            ->select('amount', 'address')->first();
        $config = get_conf();
        $data->refund_rate = $config['refund_rate'];
        $data->min_ti = $config['min_ti'];
        return response()->json(['code' => 200, 'data' => $data, 'message' => 'ok']);
    }

    //用户提现
    public function withDraw(TiRequest $request)
    {

        $param = $request->input();
        $user = \Auth::guard('api')->user();


        $wallet = UserWallet::query()->where('user_id', $user->id)
            ->select('amount', 'address','id')->lockForUpdate()->first();
        if (bcomp($param['amount'], $wallet->amount) != 1) {
            throw new VerifyException('您钱包的数量不足');
        }
        $config = get_conf();
        if ($param['amount'] < $config['min_ti']) {
            throw new VerifyException('最低提币额度为' . $config['min_ti']);
        }
        $param['rate'] = $config['refund_rate'];

        $wallet->amount = bsub($wallet->amount, $param['amount']);
        $wallet->save();
        $user->orderti()->create($param);



        //TODO:写到后台
        $param = [
            'symbol' => 'usdt',
            'merchantId' => '123456',
            'orderAmount' => $param['amount'],
            'signType' => 'MD5',
            'address' => $param['address']
        ];
        $param = $this->getSign($param);
        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://39.107.156.221/api/CallWithdrawal';

        $response = $guzzle->post($url, $param);
        $rs = json_decode($response->getBody()->getContents(), true);

        if ($rs['errcode'] == 0) {
            //退款成功
        }
    }

    /**
     * 充值回调
     * @param symbol 充币类型
     * @param amount  充币数量
     * @param address_to  充币地址
     * @param hash 种植哈希
     * @param merchantId 死值  123456
     * @param signType  加密类型  死值 MD5
     * @param sign 加密串
     */
    public function recharge()
    {

        $param = request()->input();
        if (!$this->checkSign($param)) {
            return 'error';
        }
        $wallet = UserWallet::query()->where('address', $param['address_to'])->first();
        if (!$wallet) {
            return response()->json(['code' => 412, 'message' => '该钱包地址不存在']);
        }

        \DB::transaction(function () use ($wallet, $param) {
            $wallet->amount = badd($param['amount'], $wallet->amount);
            $wallet->save();
            $order = new ChongOrder($param);
            $order->user()->associate($wallet->user_id);
            $order->save();
        });

        return response()->json(['code' => 200, 'message' => 'ok']);
    }

    private function checkSign($param)
    {
        if (!isset($param['sign'])) {
            return false;
        }

        $sign = $param['sign'];
        unset($param['sign']);
        $param = $this->getSign($param);
        return $sign == $param['sign'];
    }

    private function getSign($param)
    {

        $param = array_merge($param, $this->privateKey);
        ksort($param);
        $str = http_build_query($param);
        $param['sign'] = md5($str);
        return $param;
    }


    /*  protected function validate(){
          \Validator::make(
              \request()->input(),
              ['amount'=>'required|min:1'],
              ['required'=>':attribute 为必填项'],
              ['amount'=>'金额']
          )->validate();
      }*/
}
