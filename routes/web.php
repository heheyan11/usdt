<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('up',function(){
    return view('test.upload') ;
});

Route::view('/', 'reg');

Route::post('register','LoginController@register');
Route::post('sms','LoginController@sms');

