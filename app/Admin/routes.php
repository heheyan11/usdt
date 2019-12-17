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

    $router->resource('users', UserController::class);

    $router->get('users-tree','UserController@tree');
    $router->get('users-selftree','UserController@selftree')->name('self.tree');

    $router->get('orderti','OrderController@ti');
    $router->get('orderchong','OrderController@chong');
    $router->get('orderincome','OrderController@income');
    $router->get('orderlevel','OrderController@level');

    $router->get('form-crow','LogController@crow');



});
