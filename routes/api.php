<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Middleware\DoctorMiddleware;
use App\Http\Middleware\PatientMiddleware;
use App\Http\Middleware\TwoFactor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleWare;
use App\Http\Middleware\SecretaryMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

Route::group([
    'middleware' => 'api'
], function ($router) {
    Route::post('/varify', [TwoFactorController::class, 'varify']);
    Route::get('/resendCode', [TwoFactorController::class, 'resendCode']);
});
Route::group(
    ['middleware' => ['api', 'auth', AdminMiddleWare::class, TwoFactor::class]],
    function ($router) {
        Route::post('admin/secretary', [AdminController::class, 'createSecretary']);
        Route::put('admin/secretary', [AdminController::class, 'updateSecretary']);
        Route::delete('admin/secretary', [AdminController::class, 'deleteSecretary']);
        Route::post('admin/doctor', [AdminController::class, 'createDoctor']);

        Route::delete('admin/doctor/{id}', [AdminController::class, 'deleteDoctor']);
        Route::post('admin/department', [AdminController::class, 'createDepartment']);

        Route::delete('admin/department/{id}', [AdminController::class, 'deleteDepartment']);
        Route::delete('admin/user/{id}', [AdminController::class, 'deleteUser']);
    }
);

Route::group([
    'middleware' => [TwoFactor::class, DoctorMiddleware::class, 'api', 'auth']
], function ($router) {
    Route::post('/postArticle', [DoctorController::class, 'postArticale']);
    Route::put('/updateArticle/{id}', [DoctorController::class, 'updateArticle']);
    Route::delete('/deleteArticle/{id}', [DoctorController::class, 'deleteArticle']);
    Route::get('/getAtricles', [DoctorController::class, 'getArticles']);
    Route::get('/getAtricleById/{id}', [DoctorController::class, 'getArticleById']);
    Route::post('/imageUpload', [DoctorController::class, 'uploadImages']);
    Route::put('/updateProfile', [DoctorController::class, 'updateProfile']);
    Route::post('/imageProfileUpload', [DoctorController::class, 'uploadImagesForProfile']);
    Route::get('/getProfileImage', [DoctorController::class, 'getProfileImage']);
    Route::delete('/deleteProfileImage', [DoctorController::class, 'deleteProfileImage']);
    Route::get('/getApointments', [DoctorController::class, 'getApointments']);
    Route::post('/postPreview/{id}', [DoctorController::class, 'postPreview']);
    Route::put('/updatePreview/{id}', [DoctorController::class, 'updatePreview']);
    Route::delete('/deletePreview/{id}', [DoctorController::class, 'deletepreview']);
    Route::get('/getPreviews', [DoctorController::class, 'getPreviews']);
});

Route::group([
    'middleware' => [TwoFactor::class, PatientMiddleware::class, 'api', 'auth']
], function ($router) {
    Route::post('/pateintProfile', [PatientController::class, 'postPatientProfile']);
    Route::post('/addChild', [PatientController::class, 'addChild']);
});


Route::group([
    'middleware' => [TwoFactor::class, SecretaryMiddleware::class, 'api', 'auth']
], function ($router) {
    Route::post('secretary/leave/{id}', [SecretaryController::class, 'addMounthlyLeave']);
    Route::delete('secretary/leave', [SecretaryController::class, 'removeMonthlyLeaves']);
    Route::post('secretary/apointment',[SecretaryController::class,'reserve']);
     Route::post('secretary/apointment/{id}',[SecretaryController::class,'acceptReverse']);
     Route::delete('secretary/apointment/{id}',[SecretaryController::class,'rejectReverse']);
     Route::get('secretary/apointment',[SecretaryController::class,'appointments']);
       Route::get('secretary/apointments',[SecretaryController::class,'apointments']);
       Route::get('/secretary/search', [SecretaryController::class, 'search']);
       Route::post('secretary/rate',[SecretaryController::class,'relaseRate']);

});
//////Any Body Can Access
Route::group(['middleware' => [TwoFactor::class, 'api', 'auth']], function ($router) {
    Route::get('/doctor', [UserController::class, 'getDoctors']);
    Route::get('/department',[UserController::class, 'getDepartments']);
    Route::get('department/doctor/{departmentId}', [UserController::class, 'getDoctorsByDepartment']);
    Route::get('doctor/{id}', [UserController::class, 'getDoctor']);
    Route::get('department/{id}', [UserController::class, 'getDepartment']);
    Route::get('/leave/{id}', [UserController::class, 'getLeaves']);

    Route::get('/doctor/{dayId}/{departmentId}', [UserController::class, 'getDoctorsInDayAndDepartment']);
    Route::get('/doctors/{dayId}', [UserController::class, 'getDoctorsAndDepartment']);
    Route::get('/search', [UserController::class, 'search']);
});
