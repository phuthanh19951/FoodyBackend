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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group ( [
    'namespace'  => '\API\V1',
    'prefix'     => 'v1',
    'middleware' => 'cors'
], function () {

//  Route::resource('post','PostController');

  Route::get('province','CommonController@getProvince');
  Route::get('category','CommonController@getCategoryPost');
  Route::get('district/{provinceId}','CommonController@getDistrictByProvinceId');

  //Route for searching post
  Route::get('searchname','PostController@searchByName');
  Route::get('getimages/{postid}/{typeid?}','ImagesController@getImages');

  // Route for Post Resource
  Route::resource('posts','PostController');
  // Route for Subcomment Resource
  Route::resource('subcomments','SubCommentController');

});