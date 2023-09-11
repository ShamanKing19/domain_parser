<?php

use App\Http\Controllers\DomainController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => '/domain'], function() {
    Route::get('/', [DomainController::class, 'index']);
    Route::get('/{id}', [DomainController::class, 'view']);
    Route::get('/cms/{cms}', [DomainController::class, 'viewByCms']);
    Route::post('/create', [DomainController::class, 'store']);
    Route::post('/update', [DomainController::class, 'edit']);
});
