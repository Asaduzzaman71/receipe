<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\TutorialController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\BookmarkController;



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
Route::post('auth/user/login', [AuthController::class, 'userLogin']);

//email verification api
Route::get('/user/{userId}/verify/{otp}', [AuthController::class, 'verifyAccount']);
Route::post('/resend-otp-for-email-verify',[AuthController::class, 'resendVerificationEmail']);

//password reset
Route::post('/forgot-password', [AuthController::class, 'submitForgetPasswordForm']);
Route::post('/reset-password', [AuthController::class, 'submitResetPasswordForm']);

Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'user'
], function ($router) {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/tutorials', [HomeController::class, 'tutorialsWithBookmarksinfo']);
    Route::get('/tutorials/{id}', [TutorialController::class, 'show']);
    Route::get('/categories/{id}/tutorials', [TutorialController::class, 'tutorialByCategory']);
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::post('/profile-update', [AuthController::class, 'editProfile']);
    Route::post('/search-tutorial', [TutorialController::class, 'searchTutorialByTitle']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);
    
});


Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'auth'
], function ($router) {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::group( ['middleware' => ['jwt.verify','admin']], function(){
    Route::get('/users', [AuthController::class, 'userList']);
    Route::resource('categories', CategoryController::class);
    Route::resource('subcategories', SubCategoryController::class);
    Route::resource('ingredients', IngredientController::class);
    Route::resource('tutorials', TutorialController::class);
    Route::post('/tutorials/video-upload', [TutorialController::class, 'uploadVideo']);
    Route::resource('blogs', BlogController::class);
});


