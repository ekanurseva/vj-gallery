<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentManagementController;
use App\Http\Controllers\Admin\AdminKaryaController;
use App\Http\Controllers\Vj\VjContentController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
});

Route::middleware(['auth', 'role:vj'])->group(function () {
    Route::get('/vj/dashboard', function () {
        return view('vj.dashboard');
    });
});

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::resource('users', UserController::class);
});

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){

    Route::get('contents',
        [ContentManagementController::class,'index'])
        ->name('contents.index');
    
    Route::patch('contents/{content}/approve',
        [ContentManagementController::class,'approve'])
        ->name('contents.approve');

    Route::patch('contents/{content}/reject',
        [ContentManagementController::class,'reject'])
        ->name('contents.reject');

    Route::delete('contents/{content}', 
        [ContentManagementController::class, 'destroy'])
        ->name('contents.destroy');

    Route::post('categories',
        [CategoryController::class,'store'])
        ->name('categories.store');

    Route::delete('categories/{category}',
        [CategoryController::class,'destroy'])
        ->name('categories.destroy');

    Route::patch('categories/{category}',
        [CategoryController::class,'update'])
        ->name('categories.update');
});

Route::middleware(['auth','role:vj'])
    ->prefix('vj')
    ->name('vj.')
    ->group(function(){

    Route::get('contents/create', [ContentController::class,'create'])->name('contents.create');
    Route::post('contents', [ContentController::class,'store'])->name('contents.store');
});

Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function(){

    Route::get('karya', [AdminKaryaController::class,'index'])
        ->name('karya.index');

    Route::get('karya/create', [AdminKaryaController::class,'create'])
        ->name('karya.create');

    Route::post('karya', [AdminKaryaController::class,'store'])
        ->name('karya.store');

    Route::delete('karya/{content}', [AdminKaryaController::class,'destroy'])
        ->name('karya.destroy');
});

Route::middleware(['auth'])->prefix('vj')->group(function(){

    Route::get('/contents', [VjContentController::class,'index'])
        ->name('vj.contents.index');

    Route::get('/contents/create', [VjContentController::class,'create'])
        ->name('vj.contents.create');

    Route::post('/contents', [VjContentController::class,'store'])
        ->name('vj.contents.store');

    Route::delete('/contents/{content}', [VjContentController::class,'destroy'])
        ->name('vj.contents.destroy');

});

require __DIR__.'/auth.php';