<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    Route::post('uploadFile', 'UploadsController@uploadImg');


    $router->resource('configs', ConfController::class);

    $router->resource('articles',ArticleController::class);
    $router->resource('article-cates',ArticleCateController::class);

    $router->resource('slides', SlideController::class);
    $router->resource('crowd-fundings', CrowController::class);

    $router->resource('notices', NoticeController::class);
});
