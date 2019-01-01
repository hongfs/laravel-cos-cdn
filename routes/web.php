<?php

Route::namespace('Admin')->prefix('admin')->group(function() {
    Route::get('login', 'AuthController@login')->name('login');
    Route::post('auth', 'AuthController@auth')->name('auth');
    Route::middleware('auth')->group(function() {
        Route::get('/', 'ConsoleController@index')->name('console');
        Route::name('console.')->prefix('console')->group(function() {
            Route::get('basic', 'ConsoleController@basic')->name('basic');
            Route::get('monitor/{name}', 'MonitorController@index')->name('monitor');
        });
        Route::name('packages.')->prefix('packages')->group(function() {
            Route::get('/', 'PackagesController@index')->name('list');
            Route::get('add', 'PackagesController@create')->name('search');
            Route::get('add/{name}/', 'PackagesController@create')->name('create');
            Route::post('add/{name}/', 'PackagesController@store')->name('store');
            Route::get('log', 'PackagesController@log')->name('log');
            Route::get('log/{name}/', 'PackagesController@log');
            Route::get('{name}/', 'PackagesController@show')->name('show');
            Route::get('{name}/edit', 'PackagesController@edit')->name('edit');
            Route::put('{name}/edit', 'PackagesController@update')->name('update');
            Route::put('{name}/star/{star}', 'PackagesController@star')->name('star');
            Route::put('{name}/status/{status}', 'PackagesController@status')->name('status');
            Route::delete('{name}/', 'PackagesController@destroy')->name('destroy');
        });
        Route::match(['get', 'put'], 'setup', 'SetupController@index')->name('setup');
        Route::match(['get', 'put'], 'cache', 'CacheController@index')->name('cache');
        Route::name('personal.')->prefix('personal')->group(function() {
            Route::match(['get', 'put'], 'password', 'ResetPasswordController@index')->name('password');
        });
    });
});

Route::get('/', 'IndexController@index')->name('home');
Route::get('/{alias}/', 'IndexController@show');
Route::group(['middleware'=> 'throttle:2,10'],function(){
    Route::get('/{alias}/{version}/', 'IndexController@version')->name('version');
});
