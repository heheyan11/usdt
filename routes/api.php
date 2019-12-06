<?php

use Illuminate\Http\Request;
use Laravel\Passport\Client;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/wechat','WechatController@wechat');
Route::post('/sms','LoginController@code');

Route::post('/login','LoginController@login');
Route::post('/refresh','LoginController@refresh');
Route::get('/logout','LoginController@logout');

Route::post('/recharge','WalletController@recharge');
Route::post('/refound','WalletController@refound');

Route::get('/test',function (){
    $arr =[
        'errcode'=>0,
        'data'=>[
            'eth'=>[
                'address'=>'sdfsdafe4ffdsfdsfdsfsdfsdafsdafsdafsdafdsaf',
                'ostime'=>1332424222,
                'id'=>123213,
                'privatekey'=>'dfdfewf32f32f23f23f23f32f',
                'mnemonic'=>'fsdf sdf sdf  sadf fsda sadf sdaf sdaf ',
            ]
        ]
    ];
   return response()->json($arr);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {

    return  \Auth::guard('api')->user();

    return $request->user();
});
