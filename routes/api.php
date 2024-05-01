<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DetailController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

const URL="http://127.0.0.1:8000/";
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/otp_verify', [AuthController::class, 'otp_verify']);
    Route::post('/CheckEmail', [AuthController::class, 'CheckEmail']);
    Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
Route::group(['middleware' => ['jwt.verify']], function() {

    Route::post('/details', [DetailController::class, 'details']);
    Route::post('/info_work', [DetailController::class, 'info_work']);
    Route::post('/showUser', [DetailController::class, 'showUser']);
    Route::get('/home_list',[HomeController::class,'home']);
    Route::post('/ShowMore',[HomeController::class,'ShowMore']);
    Route::post('/upload',[ProfileController::class,'upload']);
    Route::post('/notification',[ProfileController::class,'notification']);
    Route::post('/UpdataProfile',[ProfileController::class,'UpdataProfile']);
    Route::post('/ChangePassword',[ProfileController::class,'ChangePassword']);
    Route::post('/search',[HomeController::class,'search']);
});
Route::get('/questions',[\App\Http\Controllers\Api\QusetionController:: class ,'beck'])->middleware('set.language');
Storage::disk('show');


