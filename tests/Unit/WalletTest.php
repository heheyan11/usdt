<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletTest extends TestCase
{
    private $privateKey= ['merchantkey'=>'dhasdiuahwfiuagbvkasbdiasbkcgafbasdas'];

    public function testRecharge(){
            $param = [
                'symbol'=>'usdt',
                'amount'=>500,
                'address_to'=>'adsfwefwefewfwefwe',
                'hash'=>'dfdsfdsfsdfsd',
                'merchantId'=>'123456',
                'signType'=>'MD5',
            ];
            $param['sign'] = $this->getSign($param);
            echo $param['sign'];
            //$response = $this->json('post', 'api/recharge',$param);

            /*$response
            ->assertStatus(200)
            ->assertJson([
                'code'=>200,
            ]);*/

    }


    private function checkSign($param){
        $sign = $param['sign'];
        unset($param['sign']);
        $str = $this->getSign($param);
        return $sign==$str;
    }


    private function getSign($param){
        $str = '';
        $param = array_merge($param,$this->privateKey);
        ksort($param);
        foreach ($param as $key=>$value){
            $str .= (trim($key).'='.trim($value).'&');
        }
        return  md5(substr($str,0,-1));
    }
}
