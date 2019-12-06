<?php
namespace App\Admin\Controllers;
use Encore\Admin\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadsController extends AdminController
{
    public function uploadImg(Request $request)
    {
        $file = $request->file();
        if (!empty($file)) {

            foreach ($file as $key => $value) {

                if ($value->isValid()) {

                        $disk = config('admin.upload.disk');

                        $storage = Storage::disk($disk);

                        $newFileName = $storage->put('image',$value);

                        $prefix = config('filesystems.disks.'.$disk.'.url');

                        $return = ['errno' => 0, 'data' => [$prefix.DIRECTORY_SEPARATOR.$newFileName]];

                } else {
                    return response()->json(['errno' => 1, 'info' => '错误']);
                }
            }
        } else {
            return response()->json(['errno' => 5, 'info' => '请选择文件']);
        }

        return $return;
    }
}