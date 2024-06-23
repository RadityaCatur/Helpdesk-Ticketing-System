<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Services\Acl;
use \App\Http\Controllers\SubscriberController;

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

Route::namespace('Api')->group(function () {
    Route::group(["middleware" => "auth:api"], function () {
        // Get Dashboard Information
        Route::get('/dashboard', 'DashboardController@index')->name('api.dashboard');
        Route::get('/chart', 'DashboardController@chart')->name('api.dashboard');

        Route::apiResource('users', 'UsersController');

        Route::put('users/{user}', 'UsersController@update');
        Route::get('users/{user}/permissions', 'UsersController@permissions')->middleware('role:admin');
        Route::put('users/{user}/permissions', 'UsersController@updatePermissions')->middleware('role:admin');

        Route::get('/admin', 'UserManagementController@admin');
        Route::get('/employee', 'UserManagementController@employee');

        Route::get('roles/{role}/permissions', 'UsersController@permissions')->middleware('role:admin');

        // Auth User Routes
        Route::get('/me', 'UsersController@me')->name('api.me');
        Route::get('/check-auth', 'AuthController@checkAuth')->name('api.check-auth');
        Route::post('/broadcast/auth', 'AuthController@authBroadcast');
        Route::delete('/logout', 'AuthController@logout')->name('api.logout');
        Route::post('/change-password', 'UsersController@changePassword')->name('api.change-password');
        Route::post('/change-details', 'UsersController@changeDetails')->name('api.change-details');

        // Role 
        Route::apiResource('roles', 'RoleController')->middleware('role:admin');

        // Permission
        Route::apiResource('permissions', 'PermissionController')->middleware('role:admin');

        Route::group(['prefix' => 'users', 'middleware' => 'role:admin'], function () {
            Route::get('/', 'UsersController@index');
            Route::post('/', 'UsersController@store');
            Route::put('/{id}', 'UsersController@update');
        });

        Route::group(['prefix' => 'client', 'middleware' => 'role:admin'], function () {
            Route::get('/', 'ClientController@index');
            Route::post('/', 'ClientController@store');
            Route::put('/{id}', 'ClientController@update');
        });

        Route::group(['prefix' => 'rooms'], function () {
            Route::get('/', 'RoomController@index');
            Route::get('/{room}', 'RoomController@show');
            Route::post('/{room}/setreading', 'RoomController@setreading');
            Route::post('/{room}/setemailnotification', 'RoomController@setEmailNotification');
            Route::post('/{room}/typing', 'RoomController@typing');
        });

        Route::group(['prefix' => 'messages'], function () {
            Route::get('/', 'MessageController@index');
            Route::post('/', 'MessageController@store');
            Route::post('/{room}/upload', 'MessageController@upload');
            Route::delete('/{id}', 'MessageController@destory');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'CategoryController@index');
            Route::post('/', 'CategoryController@store')->middleware('role:admin');
            Route::get('/{id}', 'CategoryController@show');
            Route::put('/{id}', 'CategoryController@update')->middleware('role:admin');
            Route::delete('/{id}', 'CategoryController@destroy')->middleware('role:admin');
        });

        Route::group(['prefix' => 'priorities'], function () {
            Route::get('/', 'PriorityController@index');
            Route::post('/', 'PriorityController@store')->middleware('role:admin');
            Route::get('/{id}', 'PriorityController@show');
            Route::put('/{id}', 'PriorityController@update')->middleware('role:admin');
            Route::delete('/{id}', 'PriorityController@destroy')->middleware('role:admin');
        });

        Route::group(['prefix' => 'projects'], function () {
            Route::get('/', 'ProjectController@index');
            Route::get('/getAll', 'ProjectController@getAll');
            Route::get('/admin', 'ProjectController@admin')->middleware('role:admin');
            Route::get('/users', 'ProjectController@users')->middleware('role:admin');
            Route::get('/client', 'ProjectController@client')->middleware('role:admin');
            Route::get('/employees', 'ProjectController@employees')->middleware('role:admin');
            Route::post('/', 'ProjectController@store')->middleware('role:admin');
            Route::get('/{id}', 'ProjectController@show');
            Route::put('/{id}', 'ProjectController@update')->middleware('role:admin');
            Route::delete('/{id}', 'ProjectController@destroy')->middleware('role:admin');
        });

        Route::group(['prefix' => 'tickets'], function () {
            Route::get('/', 'TicketController@index');
            Route::get('/export', 'TicketController@export');
            Route::post('/', 'TicketController@store');
            Route::get('/{id}', 'TicketController@show');
            Route::put('/{id}', 'TicketController@update');
            Route::delete('/{id}', 'TicketController@destroy');
            Route::get('/{id}/chat', 'TicketController@openChat');
        });

        Route::group(['prefix' => 'companies', 'middleware' => 'role:admin'], function () {
            Route::get('/', 'CompanyController@index');
            Route::post('/', 'CompanyController@store');
            Route::get('/{id}', 'CompanyController@show');
            Route::put('/{id}', 'CompanyController@update');
            Route::delete('/{id}', 'CompanyController@destroy');
        });

        Route::group(['prefix' => 'positions', 'middleware' => 'role:admin'], function () {
            Route::get('/', 'PositionController@index');
            Route::post('/', 'PositionController@store');
            Route::get('/{id}', 'PositionController@show');
            Route::put('/{id}', 'PositionController@update');
            Route::delete('/{id}', 'PositionController@destroy');
        });

        Route::get('/activities', 'ActivityLogController@index');

        Route::apiResource('broadcast', 'BroadcastController');
    });

    Route::post('/login', 'AuthController@login')->name('api.login');
    Route::post('/register', 'AuthController@register')->name('api.register');
    Route::get('/email-verification', 'VerificationController@verify')->name('verification.verify');

    Route::post('/forgot-password', 'ForgotPasswordController@sendResetLinkEmail')->name('api.forgot-password');
    Route::post('/reset-password', 'ResetPasswordController@reset')->name('api.reset-password');

    Route::get('/role-permissions', 'RolePermissionController@index')->name('api.role-permission');

    Route::get('/kirim-email', 'SubscribeController@index');
});
