<?php

use App\Http\Controllers\AnnoucementController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EntryLogController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaxController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
});

Route::get('/area/{code}', [AreaController::class, 'index'])->name('area');
Route::get('/area/{code}/latest', [AreaController::class, 'getLatest'])->name('area.latest');

Route::get('/pax', [PaxController::class, 'index'])->name('pax');
Route::get('/pax/latest', [PaxController::class, 'getLatest'])->name('pax.latest');

Route::middleware('auth', 'admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/opendoor', [DashboardController::class, 'postOpenDoor'])->name('dashboard.opendoor');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('users')->name('users.')->group(function () {
        Route::patch('menu/{id}', [UserController::class, 'patchMenu'])->name('menu.update');
        Route::patch('settings/{id}', [UserController::class, 'patchSettings'])->name('settings.update');
    });
    Route::resource('users', UserController::class);

    Route::prefix('costcenters')->name('costcenters.')->group(function () {
        Route::get('/import', [CostCenterController::class, 'getImport'])->name('import');
        Route::post('/import', [CostCenterController::class, 'postImport'])->name('import');
        Route::get('/template', [CostCenterController::class, 'getTemplate'])->name('template');
    });
    Route::resource('costcenters', CostCenterController::class);

    Route::resource('stations', StationController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('entrylogs', EntryLogController::class);
    Route::resource('annoucements', AnnoucementController::class);
    Route::resource('banners', BannerController::class);
});

require __DIR__ . '/auth.php';
