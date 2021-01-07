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

Route::group(['as' => 'api.', 'middleware' => 'auth:api'], function(){
    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('/genres', \App\Http\Controllers\GenreController::class);
    Route::resource('/cast-members', \App\Http\Controllers\CastMemberController::class);
    Route::resource('/videos', \App\Http\Controllers\VideoController::class);
});
