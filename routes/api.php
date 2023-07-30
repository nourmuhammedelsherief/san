<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeacherController\TeacherController;
use \App\Http\Controllers\Api\PublicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['cors', 'localization-api'])->group(function () {
    Route::controller(PublicController::class)->group(function () {
        Route::get('/cities', 'cities');
        Route::get('/subjects', 'subjects');
        Route::get('/cities', 'cities');
    });
    Route::prefix('teachers')->group(function () {
        Route::controller(TeacherController::class)->group(function () {
            Route::post('/login', 'login');
            Route::post('/register', 'register');
            Route::post('/verify_phone_number', 'verify_phone');
        });
    });
});
Route::group(['middleware' => ['auth:teacher-api', 'cors', 'localization-api']], function () {
    Route::prefix('teachers')->group(function () {
        Route::controller(TeacherController::class)->group(function () {
            Route::get('/profile', 'profile');
            Route::get('/barcode_url', 'barcode');
            Route::post('/change_password', 'changePassword');
            Route::post('/edit_account', 'edit_account');
            Route::post('/logout', 'logout');
        });
    });
});
