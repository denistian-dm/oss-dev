<?php

use App\Http\Controllers\API\CaseController as CaseController;
use App\Http\Controllers\API\CaseStatusController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\DivisionController;
use App\Http\Controllers\API\JuklakCategoryController;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\CaseDetailController;
use App\Http\Controllers\CategoryController;
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
Route::middleware('auth:sanctum')->get('/case-category', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->prefix('data')->group(function () {
    Route::resource('/users', UserController::class);
    Route::resource('/members', MemberController::class);
    Route::resource('/juklak', JuklakController::class);
    Route::get('/juklak/search-by-category/{category_id}', [JuklakController::class, 'findByCategory']);
    Route::resource('/case', CaseController::class);
    Route::get('/case-statistik', [CaseController::class, 'statistik']);
    Route::get('/case-new', [CaseController::class, 'new_case']);
    Route::post('/case/filter', [CaseController::class, 'filter']);
    Route::post('/case-statistik/filter', [CaseController::class, 'filter_statistik']);
    Route::post('/case-statistik/chart', [CaseController::class, 'chart']);
    Route::resource('/case-details', CaseDetailController::class);
    Route::get('case-details/{id}/show-by-id-case', [CaseDetailController::class, 'showByIdCase']);
    Route::resource('/case-status', CaseStatusController::class);
});