<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TiRequest;
use App\Models\ChongOrder;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class WalletController
{
    private $privateKey= ['merchantkey'=>'dhasdiuahwfiuagbvkasbdiasbkcgafbasdas'];


    public function index(){
        $data = UserWallet::query()->where('user_id',\Auth::guard('api')->user()->id)
            ->select('amount','address')->first();

        dd($data);
    }





    //用户提现
    public function withDraw(TiRequest $request){

        $param = $request->input();

        $param = [
          'symbol'=>'usdt',
          'merchantId'=>'123456',
          'orderAmount'=>$param['amount'],
          'signType'=>'MD5',
          'address'=>$param['address']
        ];
        $param = $this->getSign($param);
        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://39.107.156.221/api/CallWithdrawal';

        $response = $guzzle->post($url,$param);
        $rs = json_decode($response->getBody()->getContents(),true);

        if($rs['errcode']==0){
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
    public function recharge(){

        $param = request()->input();
        if(!$this->checkSign($param)){
            return 'error';
        }
        $wallet = UserWallet::query()->where('address',$param['address_to'])->first();
        if(!$wallet){
            return response()->json(['code'=>412,'message'=>'该钱包地址不存在']);
        }

        \DB::transaction(function () use ($wallet,$param){
            $wallet->amount = badd($param['amount'] , $wallet->amount);
            $wallet->save();
            $order = new ChongOrder($param);
            $order->user()->associate($wallet->user_id);
            $order->save();
        });

        return response()->json(['code'=>200,'message'=>'ok']);
    }

    private function checkSign($param){
        if(!isset($param['sign'])){
            return false;
        }

        $sign = $param['sign'];
        unset($param['sign']);
        $param = $this->getSign($param);
        return $sign==$param['sign'];
    }

    private function getSign($param){

        $param = array_merge($param,$this->privateKey);
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
