<?php

use App\Http\Controllers\ColumnController;
use App\Http\Controllers\DinasController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MasterWilayahController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ColumnGroupController;
use App\Http\Controllers\RowController;
use App\Http\Controllers\RowGroupController;
use App\Http\Controllers\TabelController;
use App\Http\Controllers\MetadataVariabelController;
use App\Models\Turtahun;
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
Route::get('/show', [HomeController::class, 'show'])->name('home.show');

Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('home.dashboard');
Route::get('/monitoring', [HomeController::class, 'monitoring'])->middleware(['auth', 'verified', 'role:admin|kominfo'])->name('home.monitoring');
Route::get('getMonitoring/{years}', [HomeController::class, 'getMonitoring'])->middleware(['auth', 'verified', 'role:admin|kominfo'])->name('home.getMonitoring');
Route::get('/getDashboard/{years}/{wilayah}', [HomeController::class, 'getDashboard'])->middleware(['auth', 'verified'])->name('home.getDashboard');

//users
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('user/index', [UserController::class, 'index'])->name('users.index');
    Route::get('user/reset', [UserController::class, 'reset'])->name('users.reset');
    Route::post('user/role', [UserController::class, 'roleChange'])->name('users.roleChange');
    Route::post('user/default', [UserController::class, 'default'])->name('users.default');
    Route::post('user/delete', [UserController::class, 'delete'])->name('users.delete');
    Route::post('user/store', [UserController::class, 'store'])->name('users.store');
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
    Route::get('dinas/fetch/{id}', [DinasController::class, 'fetch'])->name('dinas.fetch');
    Route::post('dinas/store', [DinasController::class, 'store'])->name('dinas.store');
    Route::post('dinas/update', [DinasController::class, 'update'])->name('dinas.update');
    Route::post('dinas/delete', [DinasController::class, 'delete'])->name('dinas.delete');
});
Route::get('master/wilayah/kecamatan/{kab}', [MasterWilayahController::class, 'fetchMasterKecamatan'])->name('master.wilayah.kecamatan');
Route::get('master/wilayah/desa/{kab}/{kec}', [MasterWilayahController::class, 'fetchMasterDesa'])->name('master.wilayah.desa');

//masters
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/subject/index', [SubjectController::class, 'index'])->name('subject.index');
    Route::post('/subject/store', [SubjectController::class, 'store'])->name('subject.store');
    Route::get('/subject/fetch/{id}', [SubjectController::class, 'fetch'])->name('subject.fetch');
    Route::post('subject/destroy', [SubjectController::class, 'destroy'])->name('subject.destroy');

    Route::get('/column-group/index', [ColumnGroupController::class, 'index'])->name('column_group.index');
    Route::post('/column-group/store', [ColumnGroupController::class, 'store'])->name('column_group.store');
    Route::get('/column-group/fetch/{id}', [ColumnGroupController::class, 'fetch'])->name('column_group.fetch');
    Route::post('column-group/destroy', [ColumnGroupController::class, 'destroy'])->name('column_group.destroy');

    Route::get('/column/index', [ColumnController::class, 'index'])->name('columns.index');
    Route::post('/column/store', [ColumnController::class, 'store'])->name('columns.store');
    Route::get('/column/fetch/{id}', [ColumnController::class, 'fetchForUpdate'])->name('columns.fetchForUpdate');
    Route::get('/column/fetchCreate/{id}', [ColumnController::class, 'fetchForCreate'])->name('columns.fetchForCreate');
    Route::post('column/destroy', [ColumnController::class, 'destroy'])->name('columns.destroy');

    Route::get('/row-group/index', [RowGroupController::class, 'index'])->name('row_group.index');
    Route::post('/row-group/store', [RowGroupController::class, 'store'])->name('row_group.store');
    Route::get('/row-group/fetch/{id}', [RowGroupController::class, 'fetch'])->name('row_group.fetch');
    Route::post('row-group/destroy', [RowGroupController::class, 'destroy'])->name('row_group.destroy');

    Route::get('/row/index', [RowController::class, 'index'])->name('rows.index');
    Route::post('/row/store', [RowController::class, 'store'])->name('rows.store');
    Route::get('/row/fetch/{id}', [RowController::class, 'fetchForUpdate'])->name('rows.fetchForUpdate');
    Route::get('/row/fetchCreate/{id}', [RowController::class, 'fetchForCreate'])->name('rows.fetchForCreate');
    Route::post('row/destroy', [RowController::class, 'destroy'])->name('rows.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//tabel

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tabel/index', [TabelController::class, 'index'])->name('tabel.index');
    Route::get('/tabel/entri/{id}', [TabelController::class, 'entri'])->name('tabel.entri');
    Route::post('/tabel/update-content', [TabelController::class, 'update_content'])->name('tabel.update_content');
});

Route::post('/tabel/adminHandleData', [TabelController::class, 'adminHandleData'])->middleware(['auth', 'verified', 'role:admin|kominfo'])->name('tabel.adminHandleData');
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/tabel/master', [TabelController::class, 'master'])->name('tabel.master');
    Route::get('/tabel/create', [TabelController::class, 'create'])->name('tabel.create');
    Route::post('/tabel/create', [TabelController::class, 'store'])->name('tabel.store');
    Route::post('/tabel/update', [TabelController::class, 'update'])->name('tabel.update');
    Route::get('/tabel/deletedList', [TabelController::class, 'index'])->name('tabel.deletedList');

    Route::get('/tabel/master/copy/{id}', [TabelController::class, 'copy'])->name('tabel.copy');
    Route::post('/tabel/copy', [TabelController::class, 'storeCopy'])->name('tabel.storeCopy');
    Route::get('/tabel/edit/{id}', [TabelController::class, 'edit'])->name('tabel.edit');
    Route::post('/tabel/statusDestroy', [TabelController::class, 'statusDestroy'])->name('tabel.statusDestroy');
    Route::post('/tabel/destroy', [TabelController::class, 'destroy'])->name('tabel.destroy');
});

//metadata-variabel
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('metavar/index', [MetadataVariabelController::class, 'index'])->name('metavar.index');
    Route::get('metavar/lists/{id}', [MetadataVariabelController::class, 'lists'])->name('metavar.lists');
    Route::post('metavar/store', [MetadataVariabelController::class, 'store'])->name('metavar.store');
    Route::post('metavar/update', [MetadataVariabelController::class, 'update'])->name('metavar.update');
    Route::get('metavar/fetchMaster/{id}', [MetadataVariabelController::class, 'fetchMaster'])->name('metavar.fetchMaster');
    Route::get('metavar/fetchData/{id}', [MetadataVariabelController::class, 'fetchData'])->name('metavar.fetchData');
    Route::post('metavar/destroy', [MetadataVariabelController::class, 'destroy'])->name('metavar.destroy');
    Route::get('metavar/metavarSend/{id}', [MetadataVariabelController::class, 'metavarSend'])->name('metavar.metavarSend');
});
Route::get('metavar/adminHandleMetavar', [MetadataVariabelController::class, 'adminHandleMetavar'])->middleware(['auth', 'verified', 'role:admin|kominfo'])->name('metavar.adminHandleMetavar');
Route::get('metavar/show', [MetadataVariabelController::class, 'show'])->name('metavar.show');


Route::get('fetch/data', [TabelController::class, 'getDatacontent'])->name('tabel.getDatacontent');
Route::get('/turtahun/fetch/{id}', function (string $id) {
    $target = Turtahun::leftJoin('turtahun_groups as tg', 'tg.id', '=', 'turtahuns.type')
        ->where('type', $id)
        ->get(['turtahuns.id as value', 'turtahuns.label as label', 'tg.label as tipe']);
    return response()->json(['data' => $target]);
})->name('turtahun.fetch');
require __DIR__ . '/auth.php';
