<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/v1/auth/register', [AuthController::class, 'register']);
Route::post('/v1/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/v1/auth/logout', [AuthController::class, 'logout']);
    Route::apiResource('/v1/posts', PostController::class);
    Route::post('/v1/users/{username}/follow', [FollowController::class, 'follow']);
    Route::delete('/v1/users/{username}/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/v1/users/{username}/following', [FollowController::class, 'following']);
    Route::get('/v1/users/{username}/followers', [FollowController::class, 'followers']);
    Route::put('/v1/users/{username}/accept', [FollowController::class, 'accept']);
    Route::get("/v1/users", [UserController::class, 'index']);
    Route::get("/v1/users/{username}", [UserController::class, 'show']);
});