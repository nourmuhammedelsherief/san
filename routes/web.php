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

/**
 * end admin controllers
 */

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
        Route::resource('/cities' , CityController::class);
        Route::get('/cities/delete/{id}' , [CityController::class , 'destroy']);
        // slider routes
        Route::resource('/sliders' , SliderController::class);
        Route::get('/sliders/delete/{id}' , [SliderController::class , 'destroy']);
        // subject routes
        Route::resource('/subjects' , SubjectController::class);
        Route::get('/subjects/delete/{id}' , [SubjectController::class , 'destroy']);
        // seller_codes routes
        Route::resource('/seller_codes' , SellerCodeController::class);
        Route::get('/seller_codes/delete/{id}' , [SellerCodeController::class , 'destroy']);
        // transfers routes
        Route::controller(TransferController::class)->group(function () {
            Route::get('/teacher_transfers', 'teacher_transfers')->name('teacher_transfers');
            Route::get('/teacher_transfer/{id}/{status}', 'teacher_transfer_submit')->name('teacher_transfers.submit');
        });
    });
});
/**
 * End @admin Routes
 */
