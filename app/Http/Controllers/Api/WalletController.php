<?php

namespace App\Http\Controllers\Api;

use App\Models\ChongOrder;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Http\Request;



class WalletController
{
    private $privateKey= ['merchantkey'=>'dhasdiuahwfiuagbvkasbdiasbkcgafbasdas'];

    protected function validate(){
        \Validator::make(
            \request()->input(),
            ['amount'=>'required|min:1'],
            ['required'=>':attribute 为必填项'],
            ['amount'=>'金额']
        )->validate();
    }

    public function refound(){

        $this->validate();
        $amount = \request()->input('amount');

        $wallet = User::query()->where('id',12)->first()->wallet;
        $param = [
          'symbol'=>'usdt',
          'merchantId'=>'123456',
          'orderAmount'=>$amount,
          'signType'=>'MD5',
          'address'=>$wallet->address
        ];
        $param = $this->getSign($param);
        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://39.107.156.221/api/CallWithdrawal';

        $response = $guzzle->post($url,$param);
        $rs = json_decode($response->getBody()->getContents(),true);
        dd($rs);die;

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
            return '错误';
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
        $sign = $param['sign'];
        unset($param['sign']);
        $param = $this->getSign($param);
        return $sign==$param['sign'];
    }

    private function getSign($param){
        $str = '';
        $param = array_merge($param,$this->privateKey);
        ksort($param);
        foreach ($param as $key=>$value){
            $str .= (trim($key).'='.trim($value).'&');
        }

        $param['sign'] = md5(substr($str,0,-1));
        return $param;
    }
}
