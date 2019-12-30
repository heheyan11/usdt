<?php


namespace App\Services;


use App\Exceptions\InternalException;

class CardService
{


    public function checkStr($name, $card)
    {

        $url = 'https://idenauthen.market.alicloudapi.com/idenAuthentication';
        $appcode = env('CARD_STR');
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");

        $param = ['idNo' => $card, 'name' => $name];
        $res = $this->http($url, http_build_query($param), $headers);

        if ($res['data']['respCode'] != '0000') {
            throw new InternalException($res['data']['respMessage']);
        }
        return $res['data'];
    }

    public function checkImg($file,$type)
    {

        $url = "https://dm-51.data.aliyun.com/rest/160601/ocr/ocr_idcard.json";
        $appcode = env('CARD_STR');
        //如果没有configure字段，config设为空
        $config = ["side" => $type];

        $disk = \Storage::disk('qiniu');
        try {
           $base64 = base64_encode($disk->get($file));
        }catch (\Exception $exception){
            throw new InternalException('图片不存在，请重新上传');
        }

        $headers = array();

        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/json; charset=UTF-8");

        $request = array(
            "image" => "$base64"
        );
        if (count($config) > 0) {
            $request["configure"] = json_encode($config);
        }
        $body = json_encode($request);

        $res = $this->http($url, $body, $headers);

        if($res['code']!=200){
            throw new InternalException('网络异常');
        }
        if($type=='face'){
            return [
                'address'=>$res['data']['address'],
                'birth'=>$res['data']['birth'],
                'name'=>$res['data']['name'],
                'nationality'=>$res['data']['nationality'],
                'num'=>$res['data']['num'],
                'sex'=>$res['data']['sex'],
            ];
        }else {
            return[
              'issue'=>$res['data']['issue'],
              'start_date'=>$res['data']['start_date'],
              'end_date'=>$res['data']['end_date'],
            ];
        }
    }

    public function http($url, $param, $headers, $method = 'POST')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        $res = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ['code' => $httpCode, 'data' => json_decode($res, true)];
    }
}