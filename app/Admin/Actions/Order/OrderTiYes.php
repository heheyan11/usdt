<?php

namespace App\Admin\Actions\Order;

use App\Models\OrderTi;
use App\Models\UserWallet;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class OrderTiYes extends RowAction
{
    public $name='审核通过';
    private $privateKey = ['merchantkey' => 'dhasdiuahwfiuagbvkasbdiasbkcgafbasdas'];

    public function handle(Model $model)
    {

        $param = [
            'symbol' => 'usdt',
            'merchantId' => '123456',
            'orderAmount' => $model->amount,
            'signType' => 'MD5',
            'address' =>  $model->address
        ];
        $param = $this->getSign($param);
        $guzzle = new \GuzzleHttp\Client();
        $url = 'http://39.107.156.221/api/CallWithdrawal';

        $response = $guzzle->post($url, [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params'=>$param
        ]);
        $rs = json_decode($response->getBody()->getContents(), true);
        if ($rs['errcode'] == 0) {
            $model->status = OrderTi::STATUS_YES;
            $model->save();
            return $this->response()->success('审核通过.')->refresh();
        }else{
            return $this->response()->error($rs['errmsg']);
        }
    }

    public function dialog()
    {
        $this->confirm('确定审核通过？');
    }

    private function getSign($param)
    {
        $param = array_merge($param, $this->privateKey);
        ksort($param);
        $str = http_build_query($param);
        $param['sign'] = md5($str);
        return $param;
    }
}