<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeacherController\TeacherController;
use App\Http\Controllers\Api\TeacherController\SettingController;
use App\Http\Controllers\Api\TeacherController\TClassRoomController;
use App\Http\Controllers\Api\TeacherController\StudentController;
use \App\Http\Controllers\Api\TeacherController\TeacherRateController;
use \App\Http\Controllers\Api\TeacherController\StudentRateController;
use \App\Http\Controllers\Api\TeacherController\RewardController;
use \App\Http\Controllers\Api\TeacherController\StdRewardController;
use \App\Http\Controllers\Api\TeacherController\TeacherClassIntegrationController;
use \App\Http\Controllers\Api\PublicController;

// students controllers
use \App\Http\Controllers\StudentController\AuthStudentController;

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
        Route::get('/sliders', 'sliders');
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
    Route::prefix('students')->group(function () {
        Route::controller(AuthStudentController::class)->group(function () {
            Route::post('/login', 'login');
        });
    });
});
/**
 *  Start Teacher Routes
*/
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
            Route::post('/my_class_rooms/{id}/copy', 'copy');
        });
        Route::controller(StudentController::class)->group(function () {
            Route::get('/classroom/{id}/students', 'index');
            Route::post('/classroom/{id}/students/create', 'create');
            Route::post('/classroom/students/{id}/edit', 'edit');
            Route::get('/classroom/students/{id}/show', 'show');
            Route::get('/classroom/students/{id}/delete', 'destroy');
        });
        Route::controller(TeacherRateController::class)->group(function () {
            Route::post('/rates', 'index');
            Route::post('/rates/create', 'create');
            Route::post('/rates/{id}/edit', 'edit');
            Route::get('/rates/{id}/show', 'show');
            Route::get('/rates/{id}/delete', 'destroy');
        });
        Route::controller(StudentRateController::class)->group(function () {
            Route::post('/teacher_add_student_rate', 'rate');
        });
        // rewards routes
        Route::controller(RewardController::class)->group(function () {
            Route::get('/rewards', 'index');
            Route::post('/rewards/create', 'create');
            Route::post('/rewards/{id}/edit', 'edit');
            Route::get('/rewards/{id}/show', 'show');
            Route::get('/rewards/{id}/delete', 'destroy');
        });
        Route::controller(StdRewardController::class)->group(function () {
            Route::get('/rewards_to_student/{std_id}', 'rewards_to_student');
            Route::post('/add_reward_to_student', 'add_reward_to_student');
            Route::post('/get_students_to_reward', 'get_students_to_reward');
            Route::post('/add_reward_to_students', 'add_reward_to_students');
        });

        // teachers integration with another teachers routes
        Route::controller(TeacherClassIntegrationController::class)->group(function () {
            Route::post('/integrate_with_another_teacher_request', 'integrate_with_another_teacher_request');
            Route::get('/integration_requests', 'integration_requests');
            Route::post('/teacher_apply_integration_request', 'teacher_apply_integration_request');
            Route::get('/my_integrations', 'my_integrations');
        });

    });
});
/**
 *  End Teacher Routes
 */

/**
 *  Start Student Routes
 */
Route::group(['middleware' => ['auth:student-api', 'cors', 'localization-api']], function () {
    Route::prefix('students')->group(function () {
        Route::controller(AuthStudentController::class)->group(function () {
            Route::get('/profile', 'profile');
            Route::get('/my_subscription', 'my_subscription');
            Route::post('/change_password', 'changePassword');
            Route::post('/edit_account', 'edit_account');
            Route::post('/edit_whats_info', 'edit_whats_info');
            Route::post('/logout', 'logout');
        });
    });
});
/**
 *  End Student Routes
 */
