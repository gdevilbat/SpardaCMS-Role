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
	        
			    Route::get('master', 'RoleController@index')->middleware('can:menu-role')->name('cms.role.master');
			    Route::get('form', 'RoleController@create')->name('cms.role.create');
			    Route::post('form', 'RoleController@store')->middleware('can:create-role')->name('cms.role.store');
			    Route::put('form', 'RoleController@store')->name('cms.role.store');
			    Route::post('role-scope', 'RoleController@accessScope')->middleware('can:create-role')->name('cms.role-scope.store');
			    Route::delete('destroy', 'RoleController@destroy')->name('cms.role.delete');
	        
	        /*=====  End of Account CMS  ======*/
		});

		Route::group(['prefix' => 'group'], function() {
	        /*=============================================
	        =            Member CMS                       =
	        =============================================*/
	        
			    Route::get('master', 'GroupController@index')->middleware('can:menu-role')->name('role.master');
			    Route::get('form', 'GroupController@create')->name('role.create');
			    Route::post('form', 'GroupController@store')->middleware('can:create-role')->name('role.store');
			    Route::put('form', 'GroupController@store')->name('role.store');
			    Route::delete('form', 'GroupController@destroy')->name('role.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'GroupController@serviceMaster')->middleware('can:menu-role')->name('role.service-master');
			    });
	        
	        /*=====  End of Member CMS  ======*/
		});
        
	});
});
