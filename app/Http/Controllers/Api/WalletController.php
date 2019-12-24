<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\VerifyException;
use App\Http\Requests\TiRequest;
use App\Jobs\VerifyMoney;
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

    /**
     * showdoc
     * @catalog 我的钱包
     * @title 提币
     * @description 提币
     * @method post
     * @param password 必填 string 支付密码
     * @param amount 必填 string 提币数量
     * @param address 必填 string 钱包地址
     * @url wallet/withdraw
     * @return {"code":200,"message":'提交成功，请等待审核'}
     * @remark 无
     * @number 1
     */
    public function withDraw(TiRequest $request)
    {

        $param = $request->input();
        $user = \Auth::guard('api')->user();
        $user->checkPassLimit($param['password'], 'pay');

        \DB::transaction(function () use ($user, $param) {
            $wallet = UserWallet::query()->where('user_id', $user->id)
                ->select('amount', 'address', 'id')->lockForUpdate()->first();

            $config = get_conf();
            if ($param['amount'] < $config['min_ti']) {
                throw new VerifyException('最低提币额度为' . $config['min_ti']);
            }
            $param['rate'] = $config['refund_rate'];
            $shouxu = bmul($param['amount'], bdiv($config['refund_rate'], 100));

            if (bcomp($wallet->amount, badd($param['amount'], $shouxu)) == -1) {
                throw new VerifyException('您钱包的数量不足');
            }
            $param['shouxu'] = $shouxu;
            $wallet->amount = bsub($wallet->amount, badd($param['amount'], $shouxu));
            $wallet->save();
            $ti = $user->orderti()->create($param);

            dispatch(new VerifyMoney($user->id,$ti->id));
        });
        return response()->json(['code' => 200, 'message' => '提交成功，请等待审核']);
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
