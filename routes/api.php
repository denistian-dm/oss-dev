<?php

use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\DivisionController;
use App\Http\Controllers\API\JuklakCategoryController;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\JuklakController;
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

Route::get('/division', [DivisionController::class, 'index']);
Route::middleware('auth:sanctum')->get('/level', [LevelController::class, 'index']);
Route::middleware('auth:sanctum')->get('/client', [ClientController::class, 'index']);
Route::middleware('auth:sanctum')->get('/juklak-category', [JuklakCategoryController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('data')->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('/members', MemberController::class);
    Route::resource('/juklak', JuklakController::class);
});