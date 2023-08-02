<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeacherController\TeacherController;
use App\Http\Controllers\Api\TeacherController\SettingController;
use App\Http\Controllers\Api\TeacherController\TClassRoomController;
use App\Http\Controllers\Api\TeacherController\StudentController;
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
Route::get('/check-teacher-status/{id1?}/{id2?}', [TeacherController::class , 'check_status'])->name('checkTeacherStatus');


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
            Route::post('/forget_password', 'forgetPassword');
            Route::post('/confirm_reset_code', 'confirmResetCode');
            Route::post('/reset_password', 'resetPassword');
        });
        Route::controller(SettingController::class)->group(function () {
            Route::get('/bank_info', 'bank_info');
            Route::get('/teacher_annual_subscription_value', 'teacher_annual_subscription_value');
            Route::post('/pay_annual_subscription', 'pay_annual_subscription');
        });
    });
});
Route::group(['middleware' => ['auth:teacher-api', 'cors', 'localization-api']], function () {
    Route::prefix('teachers')->group(function () {
        Route::controller(TeacherController::class)->group(function () {
            Route::get('/profile', 'profile');
            Route::get('/my_subscription', 'my_subscription');
            Route::post('/change_password', 'changePassword');
            Route::post('/edit_account', 'edit_account');
            Route::post('/edit_whats_info', 'edit_whats_info');
            Route::post('/logout', 'logout');
        });

        Route::controller(TClassRoomController::class)->group(function () {
            Route::get('/my_subjects', 'my_subjects');
            Route::get('/my_class_rooms', 'my_class_rooms');
            Route::get('/my_archived_class_rooms', 'my_archived_class_rooms');
            Route::post('/my_class_rooms/create', 'create');
            Route::post('/my_class_rooms/{id}/edit', 'edit');
            Route::get('/my_class_rooms/{id}/show', 'show');
            Route::get('/my_class_rooms/{id}/delete', 'destroy');
            Route::post('/my_class_rooms/{id}/archive', 'archive');
        });
        Route::controller(StudentController::class)->group(function () {
            Route::get('/classroom/{id}/students', 'index');
            Route::post('/classroom/{id}/students/create', 'create');
            Route::post('/classroom/students/{id}/edit', 'edit');
            Route::get('/classroom/students/{id}/show', 'show');
            Route::get('/classroom/students/{id}/delete', 'destroy');

        });

    });
});
