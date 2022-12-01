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

Route::group(['prefix' => 'control', 'middleware' => 'core.auth'], function() {
    
	Route::group(['prefix' => 'role'], function() {

		Route::group(['middleware' => 'core.menu'], function() {

	        /*=============================================
	        =            Account CMS            =
	        =============================================*/
	        
			    Route::get('master', 'RoleController@index')->middleware('can:menu-role')->name('cms.role.master');
			    Route::get('form', 'RoleController@create')->name('cms.role.create');
			    Route::post('form', 'RoleController@store')->middleware('can:create-role')->name('cms.role.store');
			    Route::put('form', 'RoleController@store')->name('cms.role.store');
			    Route::post('role-scope', 'RoleController@accessScope')->middleware('can:create-role')->name('cms.role-scope.store');
			    Route::delete('destroy', 'RoleController@destroy')->name('cms.role.delete');
	        
	        /*=====  End of Account CMS  ======*/
		});

		Route::post('data', 'RoleController@data')->middleware('can:menu-role');
        Route::post('show', 'RoleController@show');
	});
});
