<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InternalException;
use App\Exceptions\VerifyException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadContoller
{
    /**
     * showdoc
     * @catalog 工具
     * @title 上传图片
     * @description 上传图片
     * @method post
     * @url upload
     * @param file file 必选 上传域
     * @return {"code": 200,"data":"","message": "上传成功"}
     * @remark 前端判断是否登录，没有登录不能点赞
     * @number 1
     */
    public function uploadImg(Request $request)
    {
        $file = $request->file();

        if (!empty($file)) {
            foreach ($file as $key => $value) {
                $res=  $value->isValid();

                if ($res) {
                    $disk = config('admin.upload.disk');
                    $storage = Storage::disk($disk);
                    $newFileName = $storage->put('image',$value);
                    $return = ['code' => 200, 'data' => [$newFileName],'message'=>'上传成功'];

                } else {
                    throw new VerifyException('上传失败图片不能超多2M');
                }
            }
        } else {
            throw new VerifyException('请选择文件');
        }

        return $return;
    }


    public function card(){

        $url = "https://dm-51.data.aliyun.com/rest/160601/ocr/ocr_idcard.json";
        $appcode = env('CARD_STR');
        //如果没有configure字段，config设为空
        $config = ["side" => 'face'];

        $disk = \Storage::disk('qiniu');
        try {
            $base64 = base64_encode($disk->get('zheng.jpg'));
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

        dd($res);
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