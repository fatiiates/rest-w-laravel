<?php

use App\Http\Controllers\API\USER\UserController;
use App\Http\Controllers\API\USER\FileController;
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

Route::prefix('user')->group(function () {
    Route::post('login', [UserController::class, 'login']);
    Route::post('signup', [UserController::class, 'signup']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [UserController::class, 'logout']);

        Route::prefix('file')->group(function () {
            Route::post('download', [FileController::class, 'download']);
            Route::post('upload', [FileController::class, 'upload']);
            Route::post('uploadedFiles', [FileController::class, 'uploadedFiles']);
        });
        
    });
});
