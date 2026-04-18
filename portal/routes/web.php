<?php

use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\NoticeController as AdminNoticeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth','verified'])->group(function(){
    Route::get('/dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    Route::view('/admin/categories','admin.categories.index')->name('admin.categories.index');
    Route::view('/admin/categories/create','admin.categories.create')->name('admin.categories.create');
    Route::view('/admin/categories/edit','admin.categories.edit')->name('admin.categories.edit');

    Route::view('/admin/notices','admin.notices.index')->name('admin.notices.index');
    Route::view('/admin/notices/create','admin.notices.create')->name('admin.notices.create');
    Route::view('/admin/notices/edit','admin.notices.edit')->name('admin.notices.edit');

});


Route::middleware(['auth','verified'])->prefix('admin-api')->group(function(){
    Route::apiResource('categories', AdminCategoryController::class);
    Route::apiResource('notices', AdminNoticeController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
