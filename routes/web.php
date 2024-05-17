<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
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
Route::prefix('dashboard')->middleware(['auth'])->name('dashboard-')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('users/', [AdminController::class, 'users'])->name('users');
    Route::get('user/delete/{user_id}', [AdminController::class, 'deleteUser'])->name('user-delete');
    Route::get('users/export', [AdminController::class, 'exportCSV'])->name('users-export-csv');

    Route::prefix('category')->name('category-')->group(function () {
        Route::get('/{type}', [CategoryController::class, 'list']);
        Route::post('/add', [CategoryController::class, 'add'])->name('add');
        Route::post('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('delete');
    });

    Route::prefix('faqs')->name('faqs-')->group(function () {
        Route::get('/', [AdminController::class, 'faqs']);
        Route::post('add', [AdminController::class, 'addFaq'])->name('add');
        Route::post('edit/{id}', [AdminController::class, 'editFaq'])->name('edit');
        Route::get('delete-faq/{id}', [AdminController::class, 'deleteFaq'])->name('delete');
    });

});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
