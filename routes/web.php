<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentManagementController;
use App\Http\Controllers\Admin\AdminKaryaController;
use App\Http\Controllers\Vj\VjContentController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Admin\StageTemplateController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('landing');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/simulations/create/{template}', 
        [SimulationController::class,'create']
    )->name('simulations.create');

    Route::get('/simulations', 
        [SimulationController::class,'index']
    )->name('simulations.index');

    Route::delete('/simulations/{simulation}', 
        [SimulationController::class,'destroy']
    )->name('simulations.destroy');

    Route::post('/simulations/store/{template}', 
        [SimulationController::class,'store']
    )->name('simulations.store');

    Route::post(
    '/simulations/template/{simulation}/use',
        [SimulationController::class, 'useSimulationTemplate']
    )->name('simulations.useTemplate');

    Route::get('/simulations/{simulation}/builder',
        [SimulationController::class,'builder']
    )->name('simulations.builder');

    Route::get(
        '/simulations/{simulation}/reference',
        [SimulationController::class, 'reference']
    )->name('simulations.reference');

    Route::post('/simulations/{simulation}/save-layout',
        [SimulationController::class,'saveLayout']
    )->name('simulations.saveLayout');

    Route::post('/simulations/{simulation}/save-contents',
        [App\Http\Controllers\SimulationController::class, 'saveContents']
    )->name('simulations.saveContents');

    Route::post('/simulations/upload-content', 
        [SimulationController::class, 'uploadContent']
    )->name('simulations.uploadContent');

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
    Route::patch(
        'simulations/{simulation}/make-template',
        [SimulationController::class, 'makeTemplate']
    )->name('simulations.makeTemplate');
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

Route::middleware(['auth', 'role:vj'])
    ->prefix('vj')
    ->name('vj.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('vj.dashboard');
        })->name('dashboard');


        Route::get('/templates',
            [SimulationController::class, 'templates']
        )->name('templates.index');


        Route::get('/contents/create',
            [VjContentController::class, 'create']
        )->name('contents.create');


        Route::post('/contents',
            [VjContentController::class, 'store']
        )->name('contents.store');

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

Route::get('/gallery', [GalleryController::class,'index'])
    ->name('gallery.index');

Route::get('/gallery/download/{content}', [GalleryController::class,'download'])
    ->name('gallery.download');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

    Route::resource('stage_templates', StageTemplateController::class);

    Route::get('stage_templates/{stage_template}/builder',
        [StageTemplateController::class, 'builder'])
        ->name('stage_templates.builder');

    Route::post('stage_templates/{stage_template}/save-layout',
        [StageTemplateController::class, 'saveLayout'])
        ->name('stage_templates.saveLayout');
    });

require __DIR__.'/auth.php';