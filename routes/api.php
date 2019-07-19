<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//new APIs//
Route::group(['namespace' => 'API', 'as' => 'api'], function () {

	Route::post('registration', 'dataController@registration');

    Route::post('login', 'dataController@login');
	
	Route::get('getproducts', 'dataController@getproducts');
	
	Route::post('postpurchase', 'dataController@postpurchase');
	
	Route::post('cancelpurchase', 'dataController@cancelpurchase');
	
	Route::get('gethistory', 'dataController@gethistory');

});

//end of new APIS//