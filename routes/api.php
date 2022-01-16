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

Route::post('user/create','Api\UserController@create');
Route::post('user/login','Api\UserController@login');

Route::middleware('auth:sanctum')->group(function(){

    //Loan functions
    Route::post('loan/apply','Api\LoanController@apply');
    Route::post('loan/{application}/approve','Api\LoanController@approve');
    Route::get('loan/{application}','Api\LoanController@detail');
    Route::post('loan/{application}/pay','Api\LoanController@pay');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
