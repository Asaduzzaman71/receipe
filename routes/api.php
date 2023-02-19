<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\TutorialController;



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

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

//email verification api
Route::get('/user/{userId}/verify/{otp}', [AuthController::class, 'verifyAccount']);
Route::post('/resend-otp-for-email-verify',[AuthController::class, 'resendVerificationEmail']);

//password reset
Route::post('/forgot-password', [AuthController::class, 'submitForgetPasswordForm']);
Route::post('/reset-password', [AuthController::class, 'submitResetPasswordForm']);

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'auth'
], function ($router) {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::group( ['middleware' => ['jwt.verify']], function(){
    Route::get('/users', [AuthController::class, 'userList']);
    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubCategoryController::class);
    Route::resource('ingredients', IngredientController::class);
    Route::resource('tutorials', TutorialController::class);
    Route::post('tutorials/video-upload', [TutorialController::class, 'videoUpload']);
});


