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
    return \App\Models\User::with('wallet')->first();
});



Route::middleware('auth:api')->get('/user', function (Request $request) {

    return  \Auth::guard('api')->user();

    return $request->user();
});
