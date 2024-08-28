<?php

use App\Exports\BatchViewExport;
use App\Exports\ColumnGroupExport;
use App\Exports\DinasExport;
use App\Exports\MonitoringExport;
use App\Exports\RowExport;
use App\Exports\RowGroupExport;
use App\Exports\TabelListExport;
use App\Exports\UserExport;
use App\Http\Controllers\Api\HomeApiController;
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
use App\Models\Column;
use App\Models\Datacontent;
use App\Models\MetadataVariabel;
use App\Models\Row;
use App\Models\Turtahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

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
Route::get('/token', function () {
    return csrf_token();
})->name('token');
Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/show', [HomeController::class, 'show'])->name('home.show');
Route::get('/fetch-show', [HomeController::class, 'fetch'])->name('view.fetch');
Route::get('/chart-show', [HomeController::class, 'lineChart'])->name('view.chart');

Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('home.dashboard');
Route::get('/monitoring', [HomeController::class, 'monitoring'])->middleware(['auth', 'verified', 'role:admin|kominfo'])->name('home.monitoring');
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
    Route::post('user/reset-bulk', [UserController::class, 'resetBulk'])->name('users.resetBulk');
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
});
Route::middleware(['auth', 'verified', 'role:admin|kominfo'])->group(function () {

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
Route::middleware(['auth', 'verified', 'role:admin|kominfo'])->group(function () {
    Route::post('/tabel/adminHandleData', [TabelController::class, 'adminHandleData'])->name('tabel.adminHandleData');
    Route::get('/tabel/master', [TabelController::class, 'master'])->name('tabel.master');
    Route::get('/tabel/create', [TabelController::class, 'create'])->name('tabel.create');
    Route::post('/tabel/create', [TabelController::class, 'store'])->name('tabel.store');
    Route::post('/tabel/update', [TabelController::class, 'update'])->name('tabel.update');
    Route::get('/tabel/edit/{id}', [TabelController::class, 'edit'])->name('tabel.edit');
    Route::get('/tabel/master/copy/{id}', [TabelController::class, 'copy'])->name('tabel.copy');
    Route::post('/tabel/copy', [TabelController::class, 'storeCopy'])->name('tabel.storeCopy');
    Route::get('/tabel/deletedList', [TabelController::class, 'index'])->name('tabel.deletedList');
});
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::post('/tabel/statusDestroy', [TabelController::class, 'statusDestroy'])->name('tabel.statusDestroy');
    Route::post('/tabel/destroy', [TabelController::class, 'destroy'])->name('tabel.destroy');
    Route::post('/tabel/forceDelete', [TabelController::class, 'forceDeleteStatusTables'])->name('tabel.forceDelete');
    Route::post('/tabel/deleteMaster', [TabelController::class, 'deleteMaster'])->name('tabel.deleteMaster');
    Route::post('/tabel/changeStructure', [TabelController::class, 'changeStructure'])->name('tabel.changeStructure');
    Route::post('/tabel/lab-tabel', [TabelController::class, 'lab'])->name('tabel.lab');
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
    Route::post('metavar/metavarSend/{id}', [MetadataVariabelController::class, 'metavarSend'])->name('metavar.metavarSend');
});
Route::post('metavar/adminHandleMetavar', [MetadataVariabelController::class, 'adminHandleMetavar'])->middleware(['auth', 'verified', 'role:admin|kominfo'])->name('metavar.adminHandleMetavar');
Route::get('metavar/show', [MetadataVariabelController::class, 'show'])->name('metavar.show');


Route::get('fetch/data', [TabelController::class, 'getDatacontent'])->name('tabel.getDatacontent');
Route::get('/turtahun/fetch/{id}', function (string $id) {
    $target = Turtahun::leftJoin('turtahun_groups as tg', 'tg.id', '=', 'turtahuns.type')
        ->where('type', $id)
        ->get(['turtahuns.id as value', 'turtahuns.label as label', 'tg.label as tipe']);
    return response()->json(['data' => $target]);
})->name('turtahun.fetch');
Route::get('/master_metavar/fetch', function () {
    $target = MetadataVariabel::selectRaw('MIN(id) as id, r101')->groupBy('r101')->get();
    return response()->json($target);
})->name('updateMasterMetavar');
Route::get('/fetchAllColumns', function () {
    $target = Column::leftJoin('column_groups as cg', 'cg.id', '=', 'columns.id_column_groups')->get(['columns.id as value', 'columns.label as label', 'cg.label as cg_label']);
    return response()->json($target);
})->middleware(['auth', 'verified'])->name('fetchAllColumns');
Route::get('/fetchAllRows', function () {
    $target = Row::leftJoin('row_groups as cg', 'cg.id', '=', 'rows.id_row_groups')->get(['rows.id as value', 'rows.label as label', 'cg.label as cg_label']);
    return response()->json($target);
})->middleware(['auth', 'verified'])->name('fetchAllRows');
Route::post('/duplicateMaster', [TabelController::class, 'duplicateMaster'])->middleware(['auth', 'verified'])->name('duplicateMaster');
Route::get('/fetchMaster/{id}', [TabelController::class, 'fetchMaster'])->middleware(['auth', 'verified'])->name('fetchMaster');
Route::get('/order/fetch/{id}', [TabelController::class, 'fetchOrder'])->name('order.fetch')->middleware(['auth', 'verified']);
Route::post('/order/change', [TabelController::class, 'changeOrder'])->middleware(['auth', 'verified'])->name('order.changeOrder');
Route::get('/export/{id}/{title}', [MetadataVariabelController::class, 'export'])->name('export');
Route::get('/export-view/{id}/{title}', function (string $id, $title) {
    return Excel::download(new BatchViewExport($id), $title . ".xlsx");
})->name('exportView');
Route::get('/export-tabelIndex', function (Request $request) {
    $label = $request->label;
    $produsen = $request->produsen;
    $tahun = $request->tahun;
    $status = $request->status;
    $updatedBy = $request->updatedBy;
    return Excel::download(new TabelListExport($label, $produsen, $tahun, $status, $updatedBy), "tabel.xlsx");
})->name('export-tabelIndex')->middleware(['auth', 'verified']);
Route::get('/export-userIndex', function (Request $request) {
    $username = $request->username;
    $name = $request->name;
    $nama_dinas = $request->nama_dinas;
    $wilayah_label = $request->wilayah_label;
    $noHp = $request->noHp;
    $role = $request->role;
    return Excel::download(new UserExport($username, $name, $nama_dinas, $wilayah_label, $noHp, $role), "users.xlsx");
})->name('export-userIndex')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/export-dinasIndex', function (Request $request) {
    $nama = $request->nama;
    $wilayah_label = $request->wilayah_label;
    return Excel::download(new DinasExport($nama, $wilayah_label), "produsen.xlsx");
})->name('export-dinasIndex')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/export-rowGroupIndex', function (Request $request) {
    $label = $request->label;
    return Excel::download(new RowGroupExport($label), "Kelompok Baris.xlsx");
})->name('export-rowGroupIndex')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/export-columnGroupIndex', function (Request $request) {
    $label = $request->label;
    return Excel::download(new ColumnGroupExport($label), "Kelompok Kolom.xlsx");
})->name('export-columnGroupIndex')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/export-rowIndex', function (Request $request) {
    $label = $request->label;
    $rowGroupsLabel = $request->rowGroupsLabel;
    return Excel::download(new RowExport($label, $rowGroupsLabel), "Baris.xlsx");
})->name('export-rowIndex')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/export-columnIndex', function (Request $request) {
    $label = $request->label;
    $columnGroupsLabel = $request->columnGroupsLabel;
    return Excel::download(new RowExport($label, $columnGroupsLabel), "Kolom.xlsx");
})->name('export-columnIndex')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/export-monitoring', function (Request $request) {
    $label = $request->nama_dinas;
    $tahun = $request->years;
    $wilayah = $request->wilayah;
    return Excel::download(new MonitoringExport($label, $tahun, $wilayah), "Monitoring.xlsx");
})->name('export-monitoring')->middleware(['auth', 'verified', 'role:admin|kominfo']);
Route::get('/download-template/{name}', function (String $name) {
    $filePath = public_path('templates/' . $name . '.xlsx');
    return Response::download($filePath);
})->name('downloadTemplate')->middleware(['auth', 'verified', 'role:admin']);

Route::get('/download-api-how-to', function () {
    $filePath = public_path('api-how-to/API.pdf');
    return Response::download($filePath);
})->name('download-api-how-to')->middleware(['auth', 'verified', 'role:admin|kominfo']);

Route::get('/labFetch', function (Request $request) {
    $tahun = $request->tahun;
    if (in_array('all', $tahun)) $tahun = null;

    $target = Datacontent::where('id_tabel', $request->id_tabel);

    if ($tahun != null) $target->whereIn('tahun', $tahun);
    $id_row = Row::whereIn('id', $target->pluck('id_row')->toArray())
        ->get(['id as value', 'label as label']);
    $id_column = Column::whereIn('id', $target->pluck('id_column')->toArray())
        ->get(['id as value', 'label as label']);
    return response()->json([
        'row' => $id_row,
        'column' => $id_column
    ]);
})->middleware(['auth', 'verified', 'role:admin']);

Route::get('/api/home/{key}', [HomeApiController::class, 'index'])->name('home-api.index');
Route::get('/api/home/view/{key}', [HomeApiController::class, 'view'])->name('home-api.view');
Route::get('/api/master/{key}', [HomeApiController::class, 'list'])->name('home-api.master');

Route::get('/api/create', [HomeApiController::class, 'create'])
    ->middleware(['auth', 'verified', 'role:admin|kominfo'])
    ->name('home-api.create');

Route::post('/api/create', [HomeApiController::class, 'create'])
    ->middleware(['auth', 'verified', 'role:admin']);
require __DIR__ . '/auth.php';
