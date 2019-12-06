<?php

namespace App\Http\Controllers\Api;

use App\Models\ChongOrder;
use App\Models\UserWallt;
use Illuminate\Http\Request;



class WalletController
{
    private $privateKey= ['merchantkey'=>'dhasdiuahwfiuagbvkasbdiasbkcgafbasdas'];

    public function refound(){

        $param = [
          'symbol'=>'usdt',
          'merchantId'=>'123456',
          'orderAmount'=>100,
          'signType'=>'MD5',
          'address'=>'dsfdsf'
        ];

        $param = $this->getSign($param);
        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://172.17.198.235/api/CallWithdrawal';

        $response = $guzzle->post($url,$param);
        $rs = json_decode($response->getBody()->getContents(),true);
        

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
        $wallet = UserWallt::query()->where('address',$param['address_to'])->first();
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
