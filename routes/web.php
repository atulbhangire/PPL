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

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('Admin/login', function () {
    return view('Admin/login');
});*/

Route::get('Admin', ['as' => '/Admin' , 'uses' => 'HomeController@index']);

Route::get('Admin/login', ['as' => '/Admin/login', 'uses' => 'HomeController@index']);

Route::post('Admin/login', ['as' => '/Admin/login', 'uses' => 'HomeController@login']);

Route::get('Admin/logout', ['as' => '/Admin/logout', 'uses'=>'HomeController@logout']);

Route::get('Admin/authenticate-MFA', ['as' => '/Admin/verify-MFA', 'uses' => 'Auth\MfaController@showMFAWithQR']);

Route::get('Admin/verify-MFA', ['as' => '/Admin/verify-MFA', 'uses' => 'Auth\MfaController@showMFAWithoutQR']);

Route::post('Admin/verifysuadminmfa',['as' => '/Admin/verifysuadminmfa', 'uses' => 'Auth\MfaController@verifysuadminmfa']);

Route::get('Admin/SuperAdminDashboard', ['as' => '/Admin/SuperAdminDashboard', 'uses' => 'SuperAdmin\SuperAdminController@showSuperAdminDashboard']);

Route::get('Admin/SuperAdmin/AddAdmin', ['as' => '/Admin/SuperAdmin/AddAdmin', 'uses' => 'SuperAdmin\SuperAdminController@showAddAdminView']);

Route::post('Admin/SuperAdmin.getAdmin', ['as' => '/Admin/SuperAdmin.getAdmin', 'uses' => 'SuperAdmin\SuperAdminController@getAdmin']);

Route::group(['middleware' => ['web']], function(){
	Route::get('/vuejscrud', 'BlogController@vueCrud');
	Route::resource('vueitems', 'BlogController');
});
