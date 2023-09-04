<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

/**
 * start admin controllers
 */

use \App\Http\Controllers\AdminController\HomeController;
use \App\Http\Controllers\AdminController\Admin\LoginController;
use \App\Http\Controllers\AdminController\Admin\ForgotPasswordController;
use \App\Http\Controllers\AdminController\AdminController;
use \App\Http\Controllers\AdminController\SettingController;
use \App\Http\Controllers\AdminController\CityController;
use \App\Http\Controllers\AdminController\SubjectController;
use \App\Http\Controllers\AdminController\SellerCodeController;
use \App\Http\Controllers\AdminController\TransferController;
use \App\Http\Controllers\AdminController\SliderController;
use \App\Http\Controllers\AdminController\TeacherController;

/**
 * end admin controllers
 */

// school controllers
use \App\Http\Controllers\SchoolController\SchoolHomeController;
use \App\Http\Controllers\SchoolController\School\SchoolLoginController;
use \App\Http\Controllers\SchoolController\SchoolController;
use \App\Http\Controllers\SchoolController\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('locale/{locale}', function ($locale) {
    session(['locale' => $locale]);
    App::setLocale($locale);
    return redirect()->back();
})->name('language');

Route::get('/error', function () {
    echo trans('messages.errorOccurred');
});
Route::get('/check-school-status/{id1?}/{id2?}', [SchoolLoginController::class, 'check_status'])->name('checkSchoolStatus');
Route::get('/check-school-subscription-status/{id1?}/{id2?}', [SubscriptionController::class, 'check_status'])->name('checkSchoolStatus');

/**
 * Start @admin Routes
 */
Route::get('/admin/home', [HomeController::class, 'index'])->name('admin.home');
Route::prefix('admin')->group(function () {

    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('admin.login');
        Route::post('login', 'login')->name('admin.login.submit');
        Route::post('logout', 'logout')->name('admin.logout');
    });
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('admin.password.request');
        Route::post('password/email', 'sendResetLinkEmail')->name('admin.password.email');
        Route::get('password/reset/{token}', 'showResetForm')->name('admin.password.reset');
        Route::post('password/reset', 'reset')->name('admin.password.update');
    });

    Route::group(['middleware' => ['web', 'auth:admin']], function () {
        // Admins Route
        Route::resource('admins', AdminController::class, []);
        Route::controller(AdminController::class)->group(function () {
            Route::get('/profile', 'my_profile');
            Route::post('/profileEdit', 'my_profile_edit');
            Route::get('/profileChangePass', 'change_pass');
            Route::post('/profileChangePass', 'change_pass_update');
            Route::get('/admin_delete/{id}', 'admin_delete');
        });
        Route::controller(SettingController::class)->group(function () {
            Route::get('/settings', 'setting')->name('settings.index');
            Route::post('/settings', 'store_setting')->name('store_setting');
            Route::get('/about_us', 'about')->name('about');
            Route::post('/update_about', 'update_about')->name('update_about');
        });
        // city routes
        Route::resource('/cities', CityController::class);
        Route::get('/cities/delete/{id}', [CityController::class, 'destroy']);
        // slider routes
        Route::resource('/sliders', SliderController::class);
        Route::get('/sliders/delete/{id}', [SliderController::class, 'destroy']);
        // subject routes
        Route::resource('/subjects', SubjectController::class);
        Route::get('/subjects/delete/{id}', [SubjectController::class, 'destroy']);
        // seller_codes routes
        Route::resource('/seller_codes', SellerCodeController::class);
        Route::get('/seller_codes/delete/{id}', [SellerCodeController::class, 'destroy']);
        // transfers routes
        Route::controller(TransferController::class)->group(function () {
            Route::get('/teacher_transfers', 'teacher_transfers')->name('teacher_transfers');
            Route::get('/teacher_transfer/{id}/{status}', 'teacher_transfer_submit')->name('teacher_transfers.submit');
            Route::get('/school_transfers', 'schools_transfers')->name('school_transfers');
            Route::get('/school_transfers/{id}/{status}', 'schools_transfers_submit')->name('school_transfers.submit');
        });

        Route::controller(TeacherController::class)->group(function () {
            Route::get('/teachers/{status}', 'index')->name('teachers.index');
            Route::get('/classrooms', 'classrooms')->name('classrooms.index');
            Route::get('/classrooms/delete/{id}', 'delete_classroom')->name('delete_classroom');
            Route::get('/classroom_teachers/{id}', 'classroom_teachers')->name('classroom_teachers');
            Route::get('/classroom_students/{id}', 'classroom_students')->name('classroom_students');
            Route::get('/students/delete/{id}', 'delete_student')->name('delete_student');
            Route::get('/teachers/delete/{id}', 'destroy')->name('teachers.delete');
            Route::get('/parents', 'parents')->name('parents.index');
            Route::get('/parent_children/{id}', 'father_children')->name('parent_children');
            Route::get('/parents/delete/{id}', 'delete_parent')->name('delete_parent');
        });

    });
});
/**
 * End @admin Routes
 */

/**
 * Start @school Routes
 */
Route::get('/school/home', [SchoolHomeController::class, 'index'])->name('school.home');
Route::prefix('school')->group(function () {

    Route::controller(SchoolLoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('school.login');
        Route::post('login', 'login')->name('school.login.submit');
        Route::get('register', 'showRegisterForm')->name('school.register');
        Route::post('register', 'register')->name('school.register.submit');
        Route::get('register_payment/{id}', 'register_payment')->name('school.register_payment');
        Route::post('register_payment/{id}', 'submit_register_payment')->name('school.submit_register_payment');
        Route::post('logout', 'logout')->name('school.logout');
    });
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('password/reset', 'showLinkRequestForm')->name('admin.password.request');
        Route::post('password/email', 'sendResetLinkEmail')->name('admin.password.email');
        Route::get('password/reset/{token}', 'showResetForm')->name('admin.password.reset');
        Route::post('password/reset', 'reset')->name('admin.password.update');
    });

    Route::group(['middleware' => ['web', 'auth:school']], function () {

        Route::controller(SchoolController::class)->group(function () {
            Route::get('/profile', 'my_profile');
            Route::post('/profileEdit', 'my_profile_edit');
            Route::get('/profileChangePass', 'change_pass');
            Route::post('/profileChangePass', 'change_pass_update');
        });
        Route::controller(SubscriptionController::class)->group(function () {
            Route::get('/my_subscription', 'my_subscription');
            Route::get('/print_subscription_pdf', 'print_subscription_pdf');
            Route::get('/pay_subscription/{id}', 'pay_subscription')->name('pay_subscription');
            Route::post('/pay_subscription/{id}', 'submit_subscription')->name('submit_subscription');
        });

//
//        Route::controller(TeacherController::class)->group(function () {
//            Route::get('/teachers/{status}', 'index')->name('teachers.index');
//            Route::get('/classrooms', 'classrooms')->name('classrooms.index');
//            Route::get('/classrooms/delete/{id}', 'delete_classroom')->name('delete_classroom');
//            Route::get('/classroom_teachers/{id}', 'classroom_teachers')->name('classroom_teachers');
//            Route::get('/classroom_students/{id}', 'classroom_students')->name('classroom_students');
//            Route::get('/students/delete/{id}', 'delete_student')->name('delete_student');
//            Route::get('/teachers/delete/{id}', 'destroy')->name('teachers.delete');
//            Route::get('/parents', 'parents')->name('parents.index');
//            Route::get('/parent_children/{id}', 'father_children')->name('parent_children');
//            Route::get('/parents/delete/{id}', 'delete_parent')->name('delete_parent');
//        });

    });
});
/**
 * End @school Routes
 */
