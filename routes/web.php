<?php

use App\Http\Controllers\DinasController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MasterWilayahController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// })->name('/');
Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//users
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('user/index', [UserController::class, 'index'])->name('users.index');
    Route::get('user/search', [UserController::class, 'search'])->name('users.search');
    Route::get('user/reset', [UserController::class, 'reset'])->name('users.reset');
    Route::post('user/role', [UserController::class, 'roleChange'])->name('users.roleChange');
    Route::post('user/default', [UserController::class, 'default'])->name('users.default');
    Route::post('user/delete', [UserController::class, 'delete'])->name('users.delete');
    Route::post('user/add', [UserController::class, 'addUser'])->name('users.add');
    Route::get('user/create', [UserController::class, 'create'])->name('users.create');
    Route::get('user/edit', [UserController::class, 'edit'])->name('users.edit');
});
Route::get('user/edit', [UserController::class, 'edit'])->middleware(['auth', 'verified'])->name('users.edit');
Route::post('user/editProfile', [UserController::class, 'editProfile'])->middleware(['auth', 'verified'])->name('users.editProfile');

//dinas
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/dinas', [DinasController::class, 'index']);
    Route::get('dinas/create', [DinasController::class, 'create'])->name('dinas.create');
    Route::get('dinas/index', [DinasController::class, 'index'])->name('dinas.index');
    Route::get('dinas/search', [DinasController::class, 'search'])->name('dinas.search');
    Route::post('dinas/store', [DinasController::class, 'store'])->name('dinas.store');
    Route::post('dinas/update', [DinasController::class, 'update'])->name('dinas.update');
    Route::post('dinas/delete', [DinasController::class, 'delete'])->name('dinas.delete');
});
Route::get('master/wilayah/kecamatan/{kab}', [MasterWilayahController::class, 'fetchMasterKecamatan'])->name('master.wilayah.kecamatan');
Route::get('master/wilayah/desa/{kab}/{kec}', [MasterWilayahController::class, 'fetchMasterDesa'])->name('master.wilayah.desa');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
