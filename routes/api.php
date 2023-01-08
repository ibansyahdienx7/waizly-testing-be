<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamInvitation;
use App\Http\Controllers\Api\TeamUser;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('token')->group(function () {
    Route::post('create', [TokenController::class, 'create']);
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
});

// =============================================================== TOKEN GROUP ========================================================== //
Route::middleware('auth:sanctum')->group(function () {

    // ============================================================ API AUTH ================================================= //
    Route::prefix('auth')->group(function () {
        Route::post('list', [AuthController::class, 'list']);
        Route::post('login', [AuthController::class, 'login']);
        Route::get('login/challage', [AuthController::class, 'loginWeb']);
        Route::post('edit', [AuthController::class, 'edit']);
        Route::post('edit-photo', [AuthController::class, 'editPhoto']);
        Route::post('delete', [AuthController::class, 'delete']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('forgot', [AuthController::class, 'forgot']);
        Route::post('verify-password', [AuthController::class, 'checkCode']);
        Route::post('reset-password', [AuthController::class, 'reset']);
    });
    // ========================================================= END API AUTH =============================================== //

    // ============================================================ API TEAM ================================================= //
    Route::prefix('team')->group(function () {
        Route::post('list', [TeamController::class, 'list']);
        Route::post('store', [TeamController::class, 'store']);
        Route::post('edit', [TeamController::class, 'edit']);
        Route::post('delete', [TeamController::class, 'delete']);
    });
    // ========================================================= END API TEAM =============================================== //

    // ============================================================ API TEAM INVITATION ================================================= //
    Route::prefix('team')->group(function () {
        Route::prefix('invitations')->group(function () {
            Route::post('accept/{slug_email}', [TeamInvitation::class, 'accept']);
            Route::post('list', [TeamInvitation::class, 'list']);
            Route::post('store', [TeamInvitation::class, 'store']);
            Route::post('edit', [TeamInvitation::class, 'edit']);
            Route::post('delete', [TeamInvitation::class, 'delete']);
        });
    });
    // ========================================================= END API TEAM INVITATION =============================================== //

    // ============================================================ API TEAM USER ================================================= //
    Route::prefix('team')->group(function () {
        Route::prefix('user')->group(function () {
            Route::post('list', [TeamUser::class, 'list']);
            Route::post('store', [TeamUser::class, 'store']);
            Route::post('edit', [TeamUser::class, 'edit']);
            Route::post('delete', [TeamUser::class, 'delete']);
        });
    });
    // ========================================================= END API TEAM USER =============================================== //

});
