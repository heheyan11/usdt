<?php

namespace App\Http\Controllers\Api\V1;

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
}