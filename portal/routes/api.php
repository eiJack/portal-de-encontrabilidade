<?php

use App\Http\Controllers\Api\Public\CategoryController;
use App\Http\Controllers\Api\Public\NoticeController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/public', [CategoryController::class,'home']);
Route::get('/public/categories', [CategoryController::class,'list']);
Route::get('/public/categories/{slug}', [CategoryController::class,'index']);
Route::get('/public/notices/{slug}', [NoticeController::class,'show']);