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

Route::group(['prefix' => 'control', 'middleware' => 'core.menu'], function() {
    
	Route::group(['middleware' => 'core.auth'], function() {

		Route::group(['prefix' => 'role'], function() {
	        /*=============================================
	        =            Account CMS            =
	        =============================================*/
	        
			    Route::get('master', 'RoleController@index')->middleware('can:menu-role');
			    Route::get('form', 'RoleController@create');
			    Route::post('form', 'RoleController@store')->middleware('can:create-role');
			    Route::put('form', 'RoleController@store');
			    Route::post('role-scope', 'RoleController@accessScope')->middleware('can:create-role');
			    Route::delete('destroy', 'RoleController@destroy');
	        
	        /*=====  End of Account CMS  ======*/
		});
        
	});
});
