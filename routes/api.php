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

Route::get('index/index','IndexController@index');
Route::get('index/article','IndexController@article');
Route::get('article/detail','ArticleController@detail');

Route::post('wechat','WechatController@wechat');
Route::get('sms','SecretController@code');
Route::post('smscheck','SecretController@checkcode');

Route::get('crow/index','CrowController@index');
Route::get('crow/search','CrowController@search');
Route::get('crow/detail','CrowController@detail');
Route::get('crow/logcrow','CrowController@logcrow');

Route::post('recharge','WalletController@recharge');
Route::post('withdraw','WalletController@withDraw');

Route::post('secret/setpass','SecretController@setLoginPass');


Route::post('login','LoginController@login')->name('login');
Route::post('refresh','LoginController@refresh');
Route::get('logout','LoginController@logout');



Route::group(['middleware'=>'auth:api'],function (){

    Route::post('crow/buy','CrowController@buy');
    Route::post('crow/quit','CrowController@quit');

    Route::get('wallet/index','WalletController@index');

    Route::post('secret/checkpass','SecretController@checkpass');
    Route::post('secret/changephone','SecretController@changePhone');
    Route::post('secret/setpaypass','SecretController@setPayPass');
    Route::post('secret/changepaypass','SecretController@changePayPass');

    Route::get('user/index','UserController@index');
    Route::post('user/auth','UserController@auth');
    Route::post('user/changeinfo','UserController@changeInfo');
    Route::get('user/crows','UserController@crows');

    Route::post('article/parise','ArticleController@parise');


    Route::post('upload','UploadContoller@uploadImg');

});



