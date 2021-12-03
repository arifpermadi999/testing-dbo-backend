<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//App/Http/Controllers/API

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', 'AuthController@logout');
    Route::get('user/detail', 'AuthController@get_user');
    
    Route::resource('customer', 'CustomerController');
    Route::post('customer/search', 'CustomerController@search');

    Route::resource('product', 'ProductController');
    Route::post('product/search', 'ProductController@search');

    Route::resource('order', 'OrderController');
    Route::post('order/search', 'OrderController@search');
});