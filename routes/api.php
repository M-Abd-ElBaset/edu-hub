<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Authentication routes (using JWT)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth.jwt', 'throttle:100,1'])->group(function () {
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}/progress', [CourseController::class, 'progress']);
    
    Route::get('/users/{id}/courses', [UserController::class, 'courses']);
    
    Route::post('/lessons/{id}/complete', [CourseController::class, 'complete']);
});
