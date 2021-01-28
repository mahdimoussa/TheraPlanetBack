<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [ 'as' => 'login', 'uses' =>  'AuthController@login']);
    Route::post('signup', 'AuthController@signup');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

Route::get('post/getRecent', 'PostController@getRecent');
Route::post('getUserById/{id}', 'UserController@getUserById');
Route::get('setonline/{id}', 'UserController@setOnline');
Route::get('setoffline/{id}', 'UserController@setOffline');
Route::get('users/unApprovedDocuments', 'DocumentController@unApprovedDocuments');
Route::post('document/accept/{id}', 'DocumentController@accept');
Route::post('forappoint_idot','ForgetPasswordController@forget');
Route::post('availability','CalendarController@change');
Route::post('appointment','AppointmentController@changeAppointment');

// Route::post('availability','');
Route::post('resetpassword','ForgetPasswordController@resetpassword');
Route::post('checkNew','AuthController@checkNew');



Route::middleware('auth:api')->group(function() {
    Route::resource('posts.comments.subcomments', 'SubcommentController');
    Route::resource('posts.comment', 'CommentController');
    Route::resource('document', 'DocumentController');
    Route::post('documents/checkApproved', 'DocumentController@checkApproved');

    // Route::resource('profile', 'ProfileController');

    Route::resource('users', 'UserController');
    Route::resource('review', 'ReviewController');
    Route::resource('saved', 'SavedController');
    Route::resource('tags', 'TagsController');
    Route::resource('post', 'PostController');
    Route::get('post/therapist/{user}', 'PostController@getByTherapistId');
    Route::get('post/search/{searchText}', 'PostController@search');
    Route::get('post/getByTagId/{tag}', 'PostController@getByTagId');

    Route::resource('like', 'LikeController');
    Route::resource('messages', 'MessageController')->only([
        'index',
        'store'
    ]);

});
