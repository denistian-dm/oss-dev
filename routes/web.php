<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('data/users', function () {
        return Inertia::render('CPanel/Data/User/Index');
    })->name('data.users');

    Route::get('data/members', function () {
        return Inertia::render('CPanel/Data/Member/Index');
    })->name('data.members');

    Route::get('data/juklak', function () {
        return Inertia::render('CPanel/Data/Juklak/Index');
    })->name('data.juklak');

    Route::get('data/juklak/create', function () {
        return Inertia::render('CPanel/Data/Juklak/Form');
    });

    Route::get('data/juklak/{id}/edit', function ($id) {
        return Inertia::render('CPanel/Data/Juklak/Form', [
            'id' => $id
        ]);
    });
});
