<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    Route::group([
        'prefix'        => 'telegram',
        'namespace'     => 'Telegram',
        'as'            => 'telegram.',
    ], function (Router $router) {
        $router->resource('buttons', 'ButtonsController')->names('button');
    });
});
