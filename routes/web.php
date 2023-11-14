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
use \App\Http\Controllers\AdminController\NotificationController;
use \App\Http\Controllers\AdminController\SchoolController as AdminSchool;

/**
 * end admin controllers
 */

// school controllers
use \App\Http\Controllers\SchoolController\SchoolHomeController;
use \App\Http\Controllers\SchoolController\School\SchoolLoginController;
use \App\Http\Controllers\SchoolController\SchoolController;
use \App\Http\Controllers\SchoolController\SubscriptionController;
use \App\Http\Controllers\SchoolController\ClassroomController;
use \App\Http\Controllers\SchoolController\StudentController;
use \App\Http\Controllers\SchoolController\SchoolTeacherController;
use \App\Http\Controllers\SchoolController\SchoolRewardController;
use \App\Http\Controllers\SchoolController\SchoolRateController;
use \App\Http\Controllers\SchoolController\SchoolTeacherRateController;
use \App\Http\Controllers\SchoolController\SchoolTeacherRewardController;

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
Route::get('/tamara', function () {
    tamara();
});
Route::get('/tamara_checkOut', function () {
    return redirect()->to(tamara_checkOut());
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
            Route::get('/histories/{type?}', 'histories')->name('settings.histories');
            Route::get('/histories/delete/{id}', 'delete_history')->name('settings.delete_history');
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
            Route::get('/teachers/{id}/histories', 'teacher_history')->name('teachers.teacher_history');
            Route::get('/classrooms', 'classrooms')->name('adminClassrooms.index');
            Route::get('/classrooms/delete/{id}', 'delete_classroom')->name('delete_classroom');
            Route::get('/classroom_teachers/{id}', 'classroom_teachers')->name('classroom_teachers');
            Route::get('/classroom_students/{id}', 'classroom_students')->name('classroom_students');
            Route::get('/students/delete/{id}', 'delete_student')->name('delete_student');
            Route::get('/teachers/delete/{id}', 'destroy')->name('teachers.delete');
            Route::get('/parents', 'parents')->name('parents.index');
            Route::get('/parent_children/{id}', 'father_children')->name('parent_children');
            Route::get('/parents/delete/{id}', 'delete_parent')->name('delete_parent');
        });
        Route::controller(AdminSchool::class)->group(function () {
            Route::get('/schools/{status}', 'index')->name('adminSchools.index');
            Route::get('/schools/delete/{id}', 'destroy')->name('adminSchools.delete');
            Route::get('/schools/teachers/{id}', 'schoolTeachers')->name('schoolTeachers');
            Route::get('/schools/students/{id}', 'schoolStudents')->name('schoolStudents');
            Route::get('/schools/{id}/histories', 'school_history')->name('school_history');
            Route::get('/schools/{id}/classrooms', 'school_classrooms')->name('school_classrooms');
            Route::get('/schools/{id}/classrooms/{class_id}/teachers', 'school_classroom_teachers')->name('school_classroom_teachers');
        });

        Route::controller(NotificationController::class)->group(function () {
            Route::get('/public_notification', 'public_notification')->name('public_notification');
            Route::post('/store_public_notification', 'store_public_notification')->name('store_public_notification');
            Route::get('/teacher_notifications', 'teacher_notifications')->name('teacher_notifications');
            Route::post('/store_teacher_notifications', 'store_teacher_notifications')->name('store_teacher_notifications');
            Route::get('/parent_notifications', 'parent_notifications')->name('parent_notifications');
            Route::post('/store_parent_notifications', 'store_parent_notifications')->name('store_parent_notifications');
            Route::get('/student_notifications', 'student_notifications')->name('student_notifications');
            Route::post('/store_student_notifications', 'store_student_notifications')->name('store_student_notifications');

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
            Route::get('/my_subscription', 'my_subscription')->name('school.my_subscription');
            Route::get('/print_subscription_pdf', 'print_subscription_pdf');
            Route::get('/pay_subscription/{id}', 'pay_subscription')->name('pay_subscription');
            Route::post('/pay_subscription/{id}', 'submit_subscription')->name('submit_subscription');
        });

        Route::resource('/classrooms', ClassroomController::class);
        Route::get('/classrooms/copy/{id}', [ClassroomController::class, 'copy'])->name('classrooms.copy');
        Route::post('/classrooms/copy/{id}', [ClassroomController::class, 'submit_copy'])->name('classrooms.submit_copy');
        Route::get('/classrooms/delete/{id}', [ClassroomController::class, 'destroy']);

        Route::resource('/students', StudentController::class);
        Route::get('/students/delete/{id}', [StudentController::class, 'destroy']);
        Route::get('/students/{id}/rates', [StudentController::class, 'rates'])->name('students.rates');
        Route::get('/students/{id}/rewards', [StudentController::class, 'rewards'])->name('students.rewards');

        Route::resource('/teachers', SchoolTeacherController::class);
        Route::get('/teachers/delete/{id}', [SchoolTeacherController::class, 'destroy']);

        Route::resource('/rates', SchoolRateController::class);
        Route::get('/rates/delete/{id}', [SchoolRateController::class, 'destroy']);

        Route::controller(SchoolTeacherRateController::class)->group(function () {
            Route::get('/teacher/{id}/rates', 'index')->name('schoolTeacherRate');
            Route::get('/teacher/{id}/rates/create', 'create')->name('createSchoolTeacherRate');
            Route::post('/teacher/{id}/rates/store', 'store')->name('storeSchoolTeacherRate');
            Route::get('/teacher/rates/delete/{id}', 'destroy')->name('deleteSchoolTeacherRate');
        });
        Route::controller(SchoolTeacherRewardController::class)->group(function () {
            Route::get('/teacher/{id}/rewards', 'index')->name('schoolTeacherReward');
            Route::get('/teacher/{id}/rewards/create', 'create')->name('createSchoolTeacherReward');
            Route::post('/teacher/{id}/rewards/store', 'store')->name('storeSchoolTeacherReward');
            Route::get('/teacher/rewards/delete/{id}', 'destroy')->name('deleteSchoolTeacherReward');
        });

        Route::resource('/rewards', SchoolRewardController::class);
        Route::get('/rewards/delete/{id}', [SchoolRewardController::class, 'destroy']);
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
