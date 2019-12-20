<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

Route::get('index/index','IndexController@index');
Route::get('index/article','IndexController@article');
Route::get('article/detail','ArticleController@detail');

Route::post('wechat','OauthController@wechat');
Route::post('pushcode','WechatController@wechat');
Route::post('qq','OauthController@qq');
Route::get('sms','SecretController@code');
Route::post('smscheck','SecretController@smscheck');

Route::get('crow/index','CrowController@index');
Route::get('crow/search','CrowController@search');
Route::get('crow/detail','CrowController@detail');
Route::get('crow/logcrow','CrowController@logcrow');

Route::post('recharge','WalletController@recharge');


Route::post('secret/setpass','SecretController@setLoginPass');

Route::post('login','LoginController@login')->name('login');
Route::post('refresh','LoginController@refresh');
Route::get('logout','LoginController@logout');
Route::post('upload','UploadContoller@uploadImg');

Route::get('testt',function(){
 //   app(\App\Services\SmsService::class)->sendSMSTemplate('14836549',[13379246424],['用户升级队列异常']);
    //dd(Cache::forget('test'));
    //return Cache::increment('test');
});

Route::group(['middleware'=>'auth:api'],function (){

    Route::post('crow/buy','CrowController@buy');
    Route::post('crow/quit','CrowController@quit');

    Route::get('index/help','IndexController@help');
    Route::post('index/feedback','IndexController@feedback');

    Route::get('wallet/index','WalletController@index');
    Route::post('wallet/withdraw','WalletController@withDraw');

    Route::post('secret/checkpass','SecretController@checkpass');
    Route::post('secret/changephone','SecretController@changePhone');
    Route::post('secret/setpaypass','SecretController@setPayPass');
    Route::post('secret/setpassword','SecretController@setPassPass');
    Route::post('secret/changepaypass','SecretController@changePayPass');

    Route::get('user/index','UserController@index');
    Route::post('user/auth','UserController@auth');
    Route::post('user/changeinfo','UserController@changeInfo');
    Route::get('user/crows','UserController@crows');

    Route::get('user/income','UserController@income');
    Route::get('user/incomelog','UserController@incomelog');
    Route::get('user/teamincome','UserController@teamincome');
    Route::get('user/teamincomelog','UserController@teamincomelog');
    Route::get('user/tilog','UserController@tilog');
    Route::get('user/orderchong','UserController@orderchong');
    Route::get('user/friend','UserController@friend');

    Route::post('article/parise','ArticleController@parise');

});



