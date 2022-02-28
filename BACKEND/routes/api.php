<?php

use App\Models\User;
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



Route::post('login', 'AuthController@login');
Route::get('logout', 'AuthController@logout')->middleware('auth:api');

Route::prefix('/admin')->group(function () {
    Route::get('admins/list', 'AdministrateurController@index');
    Route::get('admins/show/{id}', 'AdministrateurController@show');
    Route::post('admins/store', 'AdministrateurController@store');
    Route::post('admins/update', 'AdministrateurController@update');

    Route::get('players/list', 'PlayerController@index');
    Route::get('players/show/{id}', 'PlayerController@show');
    Route::post('players/store', 'PlayerController@store');
    Route::post('players/update', 'PlayerController@update');
    Route::post('players/update/image', 'PlayerController@update_img');
    Route::post('players/delete/image', 'PlayerController@delete_img');

    Route::get('stafs/list', 'StafController@index');
    Route::get('stafs/show/{id}', 'StafController@show');
    Route::post('stafs/store', 'StafController@store');
    Route::post('stafs/update', 'StafController@update');
    Route::post('stafs/update/image', 'StafController@update_img');
    Route::post('stafs/delete/image', 'StafController@delete_img');

    Route::get('galeries/list', 'GalerieController@index');
    Route::post('galeries/store', 'GalerieController@store');
    Route::post('galeries/update/image', 'GalerieController@update_img');
    Route::post('galeries/delete/image', 'GalerieController@delete_img');

    Route::get('posts/list', 'PostController@index');
    Route::get('posts/show/{id}', 'PostController@show');
    Route::post('posts/store', 'PostController@store');
    Route::post('posts/update', 'PostController@update');
    Route::get('posts/delete/{id}', 'PostController@delete');
    Route::post('posts/update/image', 'PostController@update_img');
    Route::post('posts/delete/image', 'PostController@delete_img');

    Route::get('rencontres/list', 'RencontreController@index');
    Route::get('rencontres/show/{id}', 'RencontreController@show');
    Route::post('rencontres/store', 'RencontreController@store');
    Route::post('rencontres/update', 'RencontreController@update');
    Route::get('rencontres/delete/{id}', 'RencontreController@delete');

    Route::get('sponsors/list', 'SponsorController@index');
    Route::get('sponsors/show/{id}', 'SponsorController@show');
    Route::post('sponsors/store', 'SponsorController@store');
    Route::post('sponsors/update', 'SponsorController@update');
    Route::get('sponsors/delete/{id}', 'SponsorController@delete');

    Route::get('jerseys/list', 'JerseyController@index');
    Route::get('jerseys/show/{id}', 'JerseyController@show');
    Route::post('jerseys/store', 'JerseyController@store');
    Route::post('jerseys/update', 'JerseyController@update');
    Route::get('jerseys/delete/{id}', 'JerseyController@delete');

    
});
