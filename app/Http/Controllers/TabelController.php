<?php

namespace App\Http\Controllers;

use App\Models\Catatan;
use App\Models\Column;
use App\Models\ColumnGroup;
use App\Models\ColumnOrder;
use App\Models\Datacontent;
use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\Notifikasi;
use App\Models\Row;
use App\Models\RowGroup;
use App\Models\RowOrder;
use App\Models\Tabel;
use App\Models\Statustables;
use App\Models\Subject;
use App\Models\Turtahun;
use App\Models\TurTahunGroup;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class TabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function generateRowLabel($rows, $wilayah_fullcodes, $uuid)
    {
        try {
            //code...
            if ($rows[0]->id == 0) {
                $wilayah_parent_code = '';
                $jenis = "DAFTAR ";

                $desa = substr($wilayah_fullcodes[0], 7, 3);
                $kec = substr($wilayah_fullcodes[0], 4, 3);
                $kab = substr($wilayah_fullcodes[0], 2, 2);
                if ($desa != '000') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 7) . '000';
                    $jenis = $jenis . "DESA DI ";
                } else if ($kec != '000') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 4) . '000' . '000';
                    $jenis = $jenis . "KECAMATAN DI ";
                } else if ($kab != '00') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 2) . '00' . '000' . '000';
                    $jenis = $jenis . "KABUPATEN DI ";
                }
                if ($wilayah_parent_code == '') {
                    $rowLabel = 'PROVINSI SULAWESI UTARA';
                } else {
                    $rowLabel = $jenis . MasterWilayah::where('wilayah_fullcode', $wilayah_parent_code)->pluck('label')[0];
                    $rowLabel = strtolower($rowLabel);
                    $rowLabel = ucwords($rowLabel);
                }
                return $rowLabel;
            } else {
                $listRowGroups = [];
                foreach ($rows as $key => $value) {
                    # code...
                    array_push($listRowGroups, $value->id_row_groups);
                }
                $isUnique = count(array_unique($listRowGroups));
                if ($isUnique > 1) {
                    $tempt = RowGroup::whereIn('id', $listRowGroups)->pluck('label');
                    $text = 'Gabungan Kelompok Baris dari : ';
                    foreach ($tempt as $key => $value) {
                        # code...
                        if ($key == sizeof($tempt) - 1) $text .= $value;
                        else $text .= $value . ' - ';
                    }
                    $rowLabel = $text;
                } else $rowLabel = RowGroup::where('id', $rows[0]->id_row_groups)->pluck('label')[0];
                return $rowLabel;
            }
        } catch (\Exception $e) {
            return response()->json(array('error' => $e->getMessage(), 'tersangka' => $uuid, 'rows' => $rows));
        }
    }
    public function index(Request $request)
    {
        //
        $this_dinas = auth()->user()->id_dinas;
        $this_role = auth()->user()->role;
        $routeName = ($request->routeName) ? $request->routeName : Route::currentRouteName();
        $number = 1;
        $query = Statustables::query();
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;
        if ($this_role == 'produsen') $query->where('dinas.id', $this_dinas);
        else $query->whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah());
        if ($routeName != 'tabel.deletedList') $query->where('statustables.status', '<', 6);
        else $query->where('statustables.status', '=', 6);


        $dataToCounted = $query->join('tabels', 'statustables.id_tabel', '=', 'tabels.id')
            ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
            ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
            ->select(
                [
                    'tabels.*',
                    'tabels.id as tabelUuid',
                    'dinas.nama as nama_dinas',
                    'statustables.tahun',
                    'sdesc.label as status',
                    'statustables.id as id_statustables',
                    'statustables.updated_at as status_updated',
                    'statustables.edited_by as edited_by',
                ]
            );
        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['label'])) {
                $query->where(DB::raw("CONCAT(tabels.nomor, ' - ', tabels.label)"), 'like', '%' . $filter['label'] . '%');
            }
            if (!empty($filter['nama_dinas'])) $query->where('dinas.nama', 'like', '%' . $filter['nama_dinas'] . '%');
            if (!empty($filter['columns'])) {
                $targetTabels = Datacontent::join('columns as c', 'c.id', '=', 'datacontents.id_column')
                    ->where('c.label', 'like', '%' . $filter['columns'] . '%')->pluck('datacontents.id_tabel')->unique();
                $query->whereIn('tabels.id', $targetTabels);
            }
            if (!empty($filter['row_label'])) {
                $targetTabels = Datacontent::join('rows as r', 'r.id', '=', 'datacontents.id_row')
                    ->where('r.label', 'like', '%' . $filter['row_label'] . '%')->pluck('datacontents.id_tabel')->unique();
                if (empty($targetTabels) || !$targetTabels || !sizeof($targetTabels) > 0) {
                    $targetTabels = Datacontent::join('master_wilayah as m', 'm.wilayah_fullcode', '=', 'datacontents.wilayah_fullcode')
                        ->where('datacontents.id_row', '=', 0)
                        ->where('m.label', 'like', '%' . $filter['row_label'] . '%')->pluck('datacontents.id_tabel')->unique();
                }
                $query->whereIn('tabels.id', $targetTabels);
            }
            if (!empty($filter['tahun'])) $query->where('statustables.tahun', 'like', '%' . $filter['tahun'] . '%');
            if (!empty($filter['status'])) $query->where('sdesc.label', 'like', '%' . $filter['status'] . '%');
            if (!empty($filter['updated'])) {
                // $targetUsers = User::where('username', 'like', '%' . $filter['updated'] . '%')->value('username');
                $query->join('users', 'statustables.edited_by', '=', 'users.id');
                $query->where(DB::raw("CONCAT(users.username, ' - ', statustables.updated_at)"), 'like', '%' . $filter['updated'] . '%');
                // dd($query->toSql());
            }
        }
        $countData = $dataToCounted->count();
        $tables = $query->paginate($paginated, ['*'], 'page', $currentPage);
        $table_objects = [];

        $listOfUuid = [];
        foreach ($tables as $key => $table) {
            $datacontents = Datacontent::where('id_tabel', $table->tabelUuid)->get();
            if (sizeof($datacontents) > 0) {
                $id_rows = $datacontents->pluck('id_row')->unique();
                $id_columns = $datacontents->pluck('id_column')->unique();
                $wilayah_fullcodes = $datacontents->pluck('wilayah_fullcode')->unique();
                $rows = Row::whereIn('id', $id_rows)->get();
                $rowLabel = $this->generateRowLabel($rows, $wilayah_fullcodes, $table->tabelUuid);
                if ($id_rows[0] == 0) $rowInputs = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)->get();
                else $rowInputs = Row::whereIn('id', $id_rows)->get();
                $columns = Column::whereIn('id', $id_columns)->get('label');
            } else {
                $rowLabel = 'Tidak ada data';
                $columns = ['Tidak ada data'];
            }
            $who_updated = User::where('id', $table->edited_by)->value('username');
            // array_push($listOfUser, $rowLabel);
            $when_updated = $table->status_updated;
            $NumberAndLabel = $table->nomor . ' - ' . $table->label;

            array_push($table_objects, [
                'number' => $number++,
                'label' => $NumberAndLabel,
                'nama_dinas' => $table->nama_dinas,
                'id' => $table->tabelUuid,
                'row_label' => $rowLabel,
                'columns' => $columns,
                'tahun' => $table->tahun,
                'status' => $table->status,
                'id_statustables' => $table->id_statustables,
                'status_updated' => $when_updated,
                'who_updated' => $who_updated,
                'rowInputs' => $rowInputs,
            ]);
        }
        if ($request->routeName) {

            return response()->json([
                'tables' => $table_objects,
                'countData' => $countData,
                'data' => $listOfUuid,
                // 'test' => $countQuery->get(),
            ]);
        }

        return Inertia::render('Tabel/Index', [
            'tables' => $table_objects,
            'countData' => $countData,
            // 'data' => $tables,
        ]);
    }
    private function backup($rows, $wilayah_fullcodes, $wilayah_parent_code, $table)
    {
        try {
            //code...
            if ($rows[0]->id == 0) {
                // dd($table->tabelUuid, $rows[0]);
                $wilayah_parent_code = '';
                $jenis = "DAFTAR ";

                $desa = substr($wilayah_fullcodes[0], 7, 3);
                $kec = substr($wilayah_fullcodes[0], 4, 3);
                $kab = substr($wilayah_fullcodes[0], 2, 2);
                // dd($kec);
                if ($desa != '000') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 7) . '000';
                    $jenis = $jenis . "DESA DI ";
                } else if ($kec != '000') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 4) . '000' . '000';
                    $jenis = $jenis . "KECAMATAN DI ";
                } else if ($kab != '00') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 2) . '00' . '000' . '000';
                    $jenis = $jenis . "KABUPATEN DI ";
                }
                if ($wilayah_parent_code == '') {
                    $rowLabel = 'PROVINSI SULAWESI UTARA';
                } else {
                    $rowLabel = $jenis . MasterWilayah::where('wilayah_fullcode', $wilayah_parent_code)->pluck('label')[0];
                    $rowLabel = strtolower($rowLabel);
                    $rowLabel = ucwords($rowLabel);
                }
            } else {
                $listRowGroups = [];
                foreach ($rows as $key => $value) {
                    # code...
                    array_push($listRowGroups, $value->id_row_groups);
                }
                $isUnique = count(array_unique($listRowGroups));
                if ($isUnique > 1) {
                    $tempt = RowGroup::whereIn('id', $listRowGroups)->pluck('label');
                    $text = 'Gabungan Kelompok Baris dari : ';
                    foreach ($tempt as $key => $value) {
                        # code...
                        if ($key == sizeof($tempt) - 1) $text .= $value;
                        else $text .= $value . ' - ';
                    }
                    $rowLabel = $text;
                } else $rowLabel = RowGroup::where('id', $rows[0]->id_row_groups)->pluck('label')[0];
            }
        } catch (\Exception $e) {
            return response()->json(array('error' => $e->getMessage(), 'tersangka' => $table->tabelUuid, 'rows' => $rows, 'wilayah_parent_code' => $wilayah_parent_code));
        }
    }
    public function master(Request $request)
    {
        //
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;
        $query = Tabel::query();
        $dataToCounted = $query->join('dinas', 'dinas.id', '=', 'tabels.id_dinas')
            ->whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->select([
                'tabels.*',
                'tabels.id as tabelUuid',
                'tabels.edited_by as edited_by',
                'tabels.updated_at as status_updated'
            ]);
        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['label'])) {
                $query->where(DB::raw("CONCAT(tabels.nomor, ' - ', tabels.label)"), 'like', '%' . $filter['label'] . '%');
            }
            if (!empty($filter['nama_dinas'])) $query->where('dinas.nama', 'like', '%' . $filter['nama_dinas'] . '%');
            if (!empty($filter['columns'])) {
                $targetTabels = Datacontent::join('columns as c', 'c.id', '=', 'datacontents.id_column')
                    ->where('c.label', 'like', '%' . $filter['columns'] . '%')->pluck('datacontents.id_tabel')->unique();
                $query->whereIn('tabels.id', $targetTabels);
            }
            if (!empty($filter['row_label'])) {
                $targetTabels = Datacontent::join('rows as r', 'r.id', '=', 'datacontents.id_row')
                    ->where('r.label', 'like', '%' . $filter['row_label'] . '%')->pluck('datacontents.id_tabel')->unique();
                if (empty($targetTabels) || !$targetTabels || !sizeof($targetTabels) > 0) {
                    $targetTabels = Datacontent::join('master_wilayah as m', 'm.wilayah_fullcode', '=', 'datacontents.wilayah_fullcode')
                        ->where('datacontents.id_row', '=', 0)
                        ->where('m.label', 'like', '%' . $filter['row_label'] . '%')->pluck('datacontents.id_tabel')->unique();
                }
                $query->whereIn('tabels.id', $targetTabels);
            }
            if (!empty($filter['tahun'])) {
                $targetTabels = Statustables::where('tahun', 'like', '%' . $filter['tahun'] . '%')->pluck('id_tabel')->unique();
                $query->whereIn('tabels.id', $targetTabels);
            }
            if (!empty($filter['status'])) $query->where('sdesc.label', 'like', '%' . $filter['status'] . '%');
            if (!empty($filter['updated'])) {
                // $targetUsers = User::where('username', 'like', '%' . $filter['updated'] . '%')->value('username');
                $query->join('users', 'edited_by', '=', 'users.id');
                $query->where(DB::raw("CONCAT(users.username, ' - ', updated_at)"), 'like', '%' . $filter['updated'] . '%');
                // dd($query->toSql());
            }
        }
        $countData = $dataToCounted->count();
        $tables = $query->paginate($paginated, ['*'], 'page', $currentPage);
        $table_objects = [];
        $number = 1;
        foreach ($tables as $key => $table) {
            $datacontents = Datacontent::where('id_tabel', $table->id)->get();
            $tahunObjects = Statustables::where('id_tabel', $table->id)->select('tahun')->distinct()->get();
            $id_statustables = Statustables::where('id_tabel', $table->id)->value('id');
            $tahuns = $tahunObjects->pluck('tahun')->toArray();
            if (sizeof($datacontents) > 0) {
                $id_rows = $datacontents->pluck('id_row')->unique();
                $id_columns = $datacontents->pluck('id_column')->unique();
                $wilayah_fullcodes = $datacontents->pluck('wilayah_fullcode')->unique();
                $rows = Row::whereIn('id', $id_rows)->get();
                if ($id_rows[0] == 0) $rowInputs = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)->get();
                else $rowInputs = Row::whereIn('id', $id_rows)->get();
                $rowLabel = $this->generateRowLabel($rows, $wilayah_fullcodes, $table->id);
                $columns = Column::whereIn('id', $id_columns)->get();
            } else {
                $rowLabel = 'Tidak ada data';
                $columns = ['Tidak ada data'];
            }
            $who_updated = User::where('id', $table->edited_by)->value('username');
            $when_updated = $table->status_updated;
            $NumberAndLabel = $table->nomor . ' - ' . $table->label;
            array_push($table_objects, [
                'number' => $number++,
                'label' => $NumberAndLabel,
                'id' => $table->tabelUuid,
                'nama_dinas' => $table->dinas->nama,
                // 'rows' => $rows,
                'row_label' => $rowLabel,
                'columns' => $columns,
                'tahuns' => $tahuns,
                'status' => $table->status,
                'id_statustables' => $id_statustables,
                'status_updated' => $when_updated,
                'who_updated' => $who_updated,
                'rowInputs' => $rowInputs,
            ]);
        }
        if ($request->routeName) {
            // dd($request);
            return response()->json([
                'tables' => $table_objects,
                'countData' => $countData,
                // 'data' => $listOfUuid,
                // 'test' => $countQuery->get(),
            ]);
        }
        return Inertia::render('Master/Tabel', [
            'tables' => $table_objects,
            'countData' => $countData,
        ]);
    }

    public function create()
    {
        $tabel = Tabel::all();
        $rowLabel = RowGroup::get(['row_groups.id as value', 'row_groups.label as label']);
        $daftar_dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')
            ->whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->get(['dinas.id as value', 'dinas.nama as label']);
        $daftar_kolom = Column::get();
        $kolom_grup = ColumnGroup::get(['column_groups.id as value', 'column_groups.label as label']);
        $subjects = Subject::get(['subjects.id as value', 'subjects.label as label']);
        $turtahun_groups = TurTahunGroup::get(['turtahun_groups.id as value', 'turtahun_groups.label as label']);
        $kabupatens = MasterWilayah::where('desa', 'like', '000')->where('kec', 'like', '000')->where('kab', 'not like', '00')->select(['wilayah_fullcode', 'label'])->get();
        return Inertia::render('Tabel/Create', [
            'tabels' => $tabel,
            'row_groups' => $rowLabel,
            'dinas' => $daftar_dinas,
            'columns' => $daftar_kolom,
            'turtahun_groups' => $turtahun_groups,
            'column_groups' => $kolom_grup,
            'subjects' => $subjects,
            'kabupatens' => $kabupatens
        ]);
    }

    //     /**
    //      * Store a newly created resource in storage.
    //      */
    public function store(Request $request)
    {
        // insert table
        // $new_tabel = Tabel::create($request->tabel);
        // dd($new_tabel);

        try {
            //code...
            DB::beginTransaction();
            $request->validate([
                'tabel.nomor' => ['required', 'string'],
                'tabel.label' => ['required'],
                'tabel.unit' => ['required'],
                'tabel.id_dinas' => ['required', 'integer'],
                'tabel.id_subjek' => ['required', 'integer'],
            ]);
            // dd($request);

            //tabel create
            // dd($request->orderRow, $request->orderColumn);

            $new_tabel = Tabel::create($request->tabel);
            $id_dinas = $request->tabel["id_dinas"];
            // //debatable
            $wilayah_fullcode_produsen = Dinas::where('id', $id_dinas)->value("wilayah_fullcode");
            // // dd($wilayah_fc);
            $id_turtahun = Turtahun::where('type', $request->id_turtahun)->get();
            // // generate datacontents
            $data_contents = [];
            $is_wilayah = $request->rows['tipe'] == 1;

            foreach ($request->rows['selected'] as $row) {
                foreach ($request->columns as $column) {
                    foreach ($id_turtahun as $period) {
                        // dd($new_tabel->id, 0, $column['value'], $request->tahun, $period->id, $row['value']);
                        // dd($is_wilayah);
                        $datacontent = [
                            'id_tabel' => $new_tabel->id,
                            'id_row' =>  $is_wilayah ? 0 : $row['value'],
                            'id_column' => $column['value'],
                            'tahun' => $request->tahun,
                            'id_turtahun' => $period->id,
                            'wilayah_fullcode' =>  $is_wilayah ? (string) $row['value'] : (string) $wilayah_fullcode_produsen,
                        ];
                        // dd($datacontent);
                        array_push($data_contents, $datacontent);
                    }
                }
            }
            // dd($data_contents);
            $newStatusTables = Statustables::create(
                [
                    'id_tabel' => $new_tabel->id,
                    'tahun' => $request->tahun,
                    'status' => 1,
                    'edited_by' => auth()->user()->id,
                ]
            );
            $rowOrder = [];
            if ($request->orderRow) {
                foreach ($request->orderRow as $key => $value) {
                    # code...
                    array_push($rowOrder, $value['value']);
                }
                $rowOrder = implode(',', $rowOrder);
                $newRowOrders = RowOrder::create([
                    'id_statustabel' => $newStatusTables->id,
                    'orders' => $rowOrder,
                ]);
            }
            $columnOrder = [];
            if ($request->orderColumn) {
                foreach ($request->orderColumn as $key => $value) {
                    # code...
                    array_push($columnOrder, $value['value']);
                }
                $columnOrder = implode(',', $columnOrder);
                $newColumnOrders = ColumnOrder::create([
                    'id_statustabel' => $newStatusTables->id,
                    'orders' => $columnOrder,
                ]);
            }
            Notifikasi::create([
                'id_statustabel' => $newStatusTables->id,
                'id_user' => auth()->user()->id,
                'komentar' => "Admin telah menambahkan tabel baru dengan judul ",
            ]);
            if (empty($data_contents)) throw new Exception('Data Error, Fail to Duplicate');
            Datacontent::insert($data_contents);
            DB::commit();
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();

            // Handle the exception (log it, show a user-friendly message, etc.)
            // For example, you can log the error:
            return response()->json(array($e->getMessage(), $row));
            // Optionally, you may throw the exception again to be caught by Laravel's exception handler
            // throw $e;
        }

        return redirect()->route('tabel.index')->with('message', 'Berhasil menambahkan tabel baru');
    }

    //     /**
    //      * Store a newly created resource in storage.
    //      */
    public function storeCopy(Request $request)
    {
        $tabel = Tabel::where('id', $request->id)->first();
        if (!$tabel) {
            return back()->with('error', 'Tabel tidak dapat Ditemukan !');
        }
        // cek if year already exists
        $tabelStatus = Statustables::where('id_tabel', $request->id)
            ->where('tahun', $request->tahun)
            ->first();
        if ($tabelStatus) {
            return back()->with('error', 'Tabel dengan tahun yang sama sudah dibuat ! ');
        }

        // get first year entry
        $firstStatus = Statustables::where('id_tabel', $request->id)
            ->first();
        $checkRowOrder = RowOrder::leftJoin('statustables as st', 'st.id', '=', 'row_orders.id_statustabel')
            ->where('st.id_tabel', $request->id)->first(['row_orders.*']);
        $checkColumnOrder = ColumnOrder::leftJoin('statustables as st', 'st.id', '=', 'column_orders.id_statustabel')
            ->where('st.id_tabel', $request->id)->first(['column_orders.*']);
        // dd($checkColumnOrder->orders);
        $oldDataContents = Datacontent::where('id_tabel', $firstStatus->id_tabel)
            ->where('tahun', $firstStatus->tahun)
            ->get();
        $listYearInput = $request->tahun;
        $newDataContents = [];
        foreach ($oldDataContents as $record) {
            foreach ($listYearInput as $key => $value) {
                # code...
                array_push(
                    $newDataContents,
                    [
                        'value' => '',
                        'id_tabel' => $record->id_tabel,
                        'id_row' => $record->id_row,
                        'id_column' => $record->id_column,
                        'id_turtahun' => $record->id_turtahun,
                        'tahun' => $value,
                        'wilayah_fullcode' => $record->wilayah_fullcode,
                    ]
                );
            }
        }
        // add new record in statustabel 
        try {
            DB::beginTransaction();
            foreach ($listYearInput as $key => $value) {
                # code...
                $newStatus = Statustables::create([
                    'id_tabel' => $request->id,
                    'tahun' => $value,
                    'status' => '1',
                    'edited_by' => auth()->user()->id,
                ]);
                if ($checkRowOrder) {
                    $insertRowOrder = $checkRowOrder->orders;
                    $newRowOrder = RowOrder::create([
                        'id_statustabel' => $newStatus->id,
                        'orders' => $insertRowOrder,
                    ]);
                }
                if ($checkColumnOrder) {
                    $insertColumnOrder = $checkColumnOrder->orders;
                    $newColumnOrder = ColumnOrder::create([
                        'id_statustabel' => $newStatus->id,
                        'orders' => $insertColumnOrder,
                    ]);
                }
            }
            Datacontent::insert($newDataContents);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
        // return response()->json('Berhasil');
        // return redirect()->route('tabel.master')->with('message', 'Berhasil Menyalin Tabel !');
    }

    public function fetchMaster(String $id)
    {
        $table = Tabel::where('id', $id)->first();
        $datacontents = Datacontent::where('id_tabel', $id)->get();
        $id_rows = [];
        $wilayah_fullcodes = [];
        foreach ($datacontents as $key => $value) {
            # code...
            array_push($id_rows, $value->id_row);
            array_push($wilayah_fullcodes, $value->wilayah_fullcode);
        }
        $dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')
            ->whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->get(['dinas.id as value', 'dinas.nama as label']);
        $isWilayahFullcodes = ($id_rows[0] == 0);
        // $wilayah = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)->get();
        $current_wilayah = MasterWilayah::getMyWilayah();
        return response()->json([
            // 'column' => $wilayah,
            'tabel' => $table,
            'dinas' => $dinas,
            'kab' => $current_wilayah['kabs'],
            'isWilayahFullcodes' => $isWilayahFullcodes
        ]);
    }

    public function duplicateMaster(Request $request)
    {
        try {
            DB::beginTransaction();
            $target = Tabel::where('id', $request->id)->first();
            if ($target->label == $request->judul || $target->id_dinas == $request->produsen) return back()->with('error', 'Tidak bisa menduplikasi dengan value yang sama');
            $newTabel = [
                'nomor' => $target->nomor,
                'label' => $request->judul,
                'unit' => $target->unit,
                'id_dinas' => $request->produsen,
                'id_subjek' => $target->id_subjek,
                'edited_by' => auth()->user()->id,
            ];
            $wilayah_fullcode_produsen = Dinas::where('id', $request->produsen)
                ->value('wilayah_fullcode');
            $new_tabel = Tabel::create($newTabel);
            $datacontents = Datacontent::where('id_tabel', $request->id)
                ->get([
                    'id_row', 'id_column', 'id_turtahun'
                ]);
            $id_rows = [];
            $id_columns = [];
            $id_turtahuns = [];
            $add_datacontent = [];
            foreach ($datacontents as $key => $value) {
                # code...
                // Check and push unique id_row
                if (!in_array($value->id_row, $id_rows)) {
                    array_push($id_rows, $value->id_row);
                }

                // Check and push unique id_column
                if (!in_array($value->id_column, $id_columns)) {
                    array_push($id_columns, $value->id_column);
                }

                // Check and push unique id_turtahun
                if (!in_array($value->id_turtahun, $id_turtahuns)) {
                    array_push($id_turtahuns, $value->id_turtahun);
                }
            }
            $targetStatusTables = Statustables::where('id_tabel', $request->id)
                ->orderBy('tahun', 'DESC')
                ->pluck('tahun')
                ->first();

            // dd($id_rows, $id_columns, $id_turtahuns);
            // if ($request->rows['selected']) {
            $used_rows = ($request->rows['selected']) ? $request->rows['selected'] : $id_rows;
            // dd($used_rows);
            foreach ($used_rows as $row) {
                foreach ($id_columns as $column) {
                    foreach ($id_turtahuns as $turtahun) {
                        $tempt = [
                            'id_tabel' => $new_tabel->id,
                            'id_row' => $request->rows['selected'] ? 0 : $row,
                            'id_column' => $column,
                            'tahun' => $targetStatusTables,
                            'id_turtahun' => $turtahun,
                            'wilayah_fullcode' => $request->rows['selected'] ? (string) $row['value'] : (string) $wilayah_fullcode_produsen
                        ];
                        array_push($add_datacontent, $tempt);
                    }
                }
            }
            // }
            $checkRowOrder = RowOrder::leftJoin('statustables as st', 'st.id', '=', 'row_orders.id_statustabel')
                ->where('st.id_tabel', $request->id)->first(['row_orders.*']);
            $checkColumnOrder = ColumnOrder::leftJoin('statustables as st', 'st.id', '=', 'column_orders.id_statustabel')
                ->where('st.id_tabel', $request->id)->first(['column_orders.*']);

            $newStatusTables = Statustables::create([
                'id_tabel' => $new_tabel->id,
                'tahun' => $targetStatusTables,
                'status' => 1,
                'edited_by' => auth()->user()->id,
            ]);
            Notifikasi::create([
                'id_statustabel' => $newStatusTables->id,
                'id_user' => auth()->user()->id,
                'komentar' => 'Admin telah menambahkan tabel baru dengan judul ',
            ]);
            if ($checkRowOrder) {
                $insertRowOrder = $checkRowOrder->orders;
                $newRowOrder = RowOrder::create([
                    'id_statustabel' => $newStatusTables->id,
                    'orders' => $insertRowOrder,
                ]);
            }
            if ($checkColumnOrder) {
                $insertColumnOrder = $checkColumnOrder->orders;
                $newColumnOrder = ColumnOrder::create([
                    'id_statustabel' => $newStatusTables->id,
                    'orders' => $insertColumnOrder,
                ]);
            }

            // Datacontent::insert($add_datacontent);
            // dd($add_datacontent);
            // $add_datacontent = [];
            if (empty($add_datacontent)) throw new Exception('Data Error, Fail to Duplicate');

            foreach ($add_datacontent as $key => $value) {
                # code...
                Datacontent::create($value);
            }
            DB::commit();
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            // return redirect()->route('tabel.master')->response()->json(array($th->getMessage()));
            return response()->json(array($th->getMessage()));
        }
        return redirect()->route('tabel.master')->with('message', 'Berhasil duplikat tabel');
    }
    //     /**
    //      * Display the specified resource.
    //      */
    public function entri(string $id)
    {
        $statusTabel = Statustables::join('tabels as t', 'statustables.id_tabel', 't.id')
            ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
            ->select(
                't.id as id_tabel',
                't.label as judul_tabel',
                'statustables.tahun',
                'statustables.status as status',
                'statustables.id as id_statustables',
                'sdesc.label as status_desc',
                'statustables.updated_at as status_updated',
            )
            ->where('statustables.id', $id)->first();

        $catatans = Catatan::where('id_statustabel', $id)->value('catatan');
        $id_tabel = $statusTabel->id_tabel;
        $tahun = $statusTabel->tahun;
        $sdesc = $statusTabel->status_desc;

        $datacontents = Datacontent::where('id_tabel', $id_tabel)->where('tahun', $tahun)->get();
        $id_rows = [];
        $wilayah_fullcodes = [];
        $id_columns = [];
        $tahuns = [];
        $turTahunKeys = [];

        foreach ($datacontents as $datacontent) {
            $split = explode("-", $datacontent->label);

            array_push($id_rows, $datacontent->id_row);
            array_push($id_columns, $datacontent->id_column);
            array_push($tahuns, $datacontent->tahun);
            array_push($turTahunKeys, $datacontent->id_turtahun);

            array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
        }
        $rows = Row::whereIn('id', $id_rows)->get();
        $rowLabel = RowGroup::where('id', $rows[0]->id_row_groups)->get();

        $RowOrders = RowOrder::where('id_statustabel', $id)->value('orders');
        try {
            //code...
            // dd($rows[0]);
            if ($rows[0]->id == 0) {
                $wilayah_parent_code = '';
                $jenis = "DAFTAR ";
                $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                    ->orderByRaw("CASE WHEN desa = '000' THEN 1 ELSE 0 END")
                    ->orderBy('desa')
                    ->get();
                $rows = $temp;
                $desa = substr($wilayah_fullcodes[0], 7, 3);
                $kec = substr($wilayah_fullcodes[0], 4, 3);
                $kab = substr($wilayah_fullcodes[0], 2, 2);
                if ($desa != '000') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 7) . '000';
                    $jenis = $jenis . "DESA DI ";
                } else if ($kec != '000') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 4) . '000' . '000';
                    $jenis = $jenis . "KECAMATAN DI ";
                    $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                        ->orderByRaw("CASE WHEN kec = '000' THEN 1 ELSE 0 END")
                        ->orderBy('desa')
                        ->get();
                    $rows = $temp;
                } else if ($kab != '00') {
                    $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 2) . '00' . '000' . '000';
                    $jenis = $jenis . "KABUPATEN DI ";
                    $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                        ->orderByRaw("CASE WHEN kab = '00' THEN 1 ELSE 0 END")
                        ->orderBy('desa')
                        ->get();
                    $rows = $temp;
                }
                if ($RowOrders) $rows = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                    ->orderByRaw("FIELD(wilayah_fullcode," . $RowOrders . ")")->get();
                if ($wilayah_parent_code == '') {
                    $rowLabel = 'PROVINSI SULAWESI UTARA';
                } else {
                    $rowLabel = $jenis . MasterWilayah::where('wilayah_fullcode', $wilayah_parent_code)->pluck('label')[0];
                    $rowLabel = strtolower($rowLabel);
                    $rowLabel = ucwords($rowLabel);
                }
            } else {
                $listRowGroups = [];
                foreach ($rows as $key => $value) {
                    # code...
                    array_push($listRowGroups, $value->id_row_groups);
                }
                $isUnique = count(array_unique($listRowGroups));
                if ($isUnique > 1) {
                    $tempt = RowGroup::whereIn('id', $listRowGroups)->pluck('label');
                    $text = 'Gabungan Kelompok Baris dari : ';
                    foreach ($tempt as $key => $value) {
                        # code...
                        if ($key == sizeof($tempt) - 1) $text .= $value;
                        else $text .= $value . ' - ';
                    }
                    $rowLabel = $text;
                } else $rowLabel = RowGroup::where('id', $rows[0]->id_row_groups)->pluck('label')[0];
            }
        } catch (\Exception $e) {
            return response()->json(array('error' => $e->getMessage(), 'rows' => $rows));
        }

        //call the orders
        $ColumnOrders = ColumnOrder::where('id_statustabel', $id)->value('orders');
        if ($ColumnOrders) {
            $columns = Column::whereIn('id', $id_columns)->orderByRaw("FIELD(id," . $ColumnOrders . ")")->get();
        } else {
            $columns = Column::whereIn('id', $id_columns)->get();
        }
        // dd($id_columns, $columns, $orders);
        if (!$rows[0]->id == 0 && $RowOrders)  $rows = Row::whereIn('id', $id_rows)->orderByRaw("FIELD(id," . $RowOrders . ")")->get();
        $tahuns = array_unique($tahuns);
        sort($tahuns);
        $turtahuns = Turtahun::whereIn('id', $turTahunKeys)->get();

        return Inertia::render('Tabel/Entri', [
            'datacontents' => $datacontents,
            'years' => $tahuns[0],
            'rows' => $rows,
            'row_label' => $rowLabel,
            'columns' => $columns,
            'turtahuns' => $turtahuns,
            'judul_tabel' => $statusTabel->judul_tabel,
            'status_updated' => $statusTabel->status_updated,
            'status_desc' => [$statusTabel->status, $sdesc],
            'catatans' => $catatans,
        ]);
    }

    public function fetchOrder(string $id)
    {
        $columnOrder = ColumnOrder::where('id_statustabel', $id)->value('orders');
        $rowOrder = RowOrder::where('id_statustabel', $id)->value('orders');
        $statustabel = Statustables::where('id', $id)->value('id_tabel');
        $tabel = Tabel::where('id', $statustabel)->value('id');

        if ($columnOrder) {
            $columnList = Column::whereIn('id', explode(',', $columnOrder))->orderByRaw("FIELD(id," . $columnOrder . ")")->get();
        } else {
            $dc = Datacontent::where('id_tabel', $tabel)->get(['id_column']);
            $columnList = Column::whereIn('id', $dc)->get();
        }
        $dc = Datacontent::where('id_tabel', $tabel)->get(['id_row']);
        if (!$dc[0]->id_row == 0) {
            if ($rowOrder) {
                $rowList = Row::whereIn('id', explode(',', $rowOrder))->orderByRaw("FIELD(id," . $rowOrder . ")")->get();
            } else {
                $rowList = Row::whereIn('id', $dc)->get();
            }
        } else $rowList = null;
        $data = [
            'columnList' => $columnList,
            'rowList' => $rowList
        ];
        return response()->json($data);
    }

    public function changeOrder(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'orders' => ['required']
        ]);
        $this_column_order = ColumnOrder::where('id_statustabel', $request->id)->value('id_statustabel');
        $this_row_order = ColumnOrder::where('id_statustabel', $request->id)->value('id_statustabel');
        // dd($request->orders);
        try {
            //code...
            DB::beginTransaction();
            //columns 
            $requested_column_order = $request->orders['columnOrder'];
            $this_to_column_order = [];
            foreach ($requested_column_order as $key => $value) {
                # code...
                array_push($this_to_column_order, $value['id']);
            }
            $this_to_column_order = implode(',', $this_to_column_order);
            if ($this_column_order) {
                ColumnOrder::where('id_statustabel', $this_column_order)->update([
                    'orders' => $this_to_column_order,
                ]);
            } else {
                ColumnOrder::create([
                    'id_statustabel' => $request->id,
                    'orders' => $this_to_column_order,
                ]);
            }

            //rows
            $requested_row_order = $request->orders['rowOrder'];
            if ($requested_row_order) {
                $this_to_row_order = [];
                foreach ($requested_row_order as $key => $value) {
                    # code...
                    array_push($this_to_row_order, $value['id']);
                }
                $this_to_row_order = implode(',', $this_to_row_order);
                if ($this_row_order) {
                    // dd('asu');
                    RowOrder::where('id_statustabel', $this_row_order)->update([
                        'orders' => $this_to_row_order,
                    ]);
                } else {
                    RowOrder::create([
                        'id_statustabel' => $request->id,
                        'orders' => $this_to_row_order,
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json($th->getMessage());
        }
        return redirect()->route('tabel.index')->with('message', 'berhasil mengubah urutan');
    }

    //     /**
    //      * Show the form for editing the specified resource.
    //      */
    public function edit(string $id)
    {
        $tabel = Tabel::where('id', $id)->first();
        $daftar_dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')
            ->whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->get(['dinas.id as value', 'dinas.nama as label']);
        $subjects = Subject::get(['subjects.id as value', 'subjects.label as label']);

        return Inertia::render('Tabel/Edit', [
            'tabel' => $tabel,
            'dinas' => $daftar_dinas,
            'subjects' => $subjects,
        ]);
    }


    //     /**
    //      * Update the specified resource in storage.
    //      */

    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'tabel.nomor' => ['required', 'string'],
                'tabel.label' => ['required'],
                'tabel.unit' => ['required'],
                'tabel.id_dinas' => ['required', 'integer'],
                'tabel.id_subjek' => ['required', 'integer'],
            ]);
            $dataUpdate = [
                'nomor' => $data['tabel']['nomor'],
                'label' => $data['tabel']['label'],
                'unit' => $data['tabel']['unit'],
                'id_dinas' => $data['tabel']['id_dinas'],
                'id_subjek' => $data['tabel']['id_subjek'],
            ];
            $tabel = Tabel::findOrFail($request->id);
            $tabel->update($dataUpdate);

            return redirect()->route('tabel.master')->with('message', 'Berhasil menyimpan perubahan !');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update_content(Request $request)
    {
        $data = $request->dataContents;
        $decisions = $request->decisions;
        try {
            //code...
            DB::beginTransaction();
            foreach ($data as $item) {
                Datacontent::where('id', $item['id'])
                    ->update([
                        'value' => $item['value']
                    ]);
            }
            Statustables::where('id_tabel', $data[0]['id_tabel'])
                ->where('tahun', $data[0]['tahun'])
                ->update([
                    'status' => ($decisions == "save") ? '2' : '3',
                    'edited_by' => auth()->user()->id,
                ]);
            $status = Statustables::where('id_tabel', $data[0]['id_tabel'])
                ->where('tahun', $data[0]['tahun'])
                ->leftJoin('status_desc', 'statustables.status', '=', 'status_desc.id')
                ->first(['statustables.*', 'status_desc.label as statuslabel']);

            if ($status->status == '3') {
                # code...
                Notifikasi::create([
                    'id_statustabel' => $status->id,
                    'id_user' => auth()->user()->id,
                    'komentar' => auth()->user()->name . " [ " . auth()->user()->dinas->nama . " ]" . " telah mengirim tabel dengan judul ",
                ]);
            }
            if ($request->catatans) {
                # code...
                $catatan = Catatan::create([
                    'id_statustabel' => $status->id,
                    'catatan' => $request->catatans,
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            // Handle the exception (log it, show a user-friendly message, etc.)
            // For example, you can log the error:
            return response()->json($th->getMessage());
        }
        // Update records in a single query

        return redirect()->route('tabel.entri', ['id' => $status->id])->with('message', 'Berhasil');
    }

    public function adminHandleData(Request $request)
    {
        $isAdmin = auth()->user()->role == "admin" |  auth()->user()->role == "kominfo";
        if (!$isAdmin) {
            return response()->json([
                'error' => 'Operasi ini hanya bisa dilakukan oleh Admin'
            ], 403);
        }
        $data = $request->dataContents;
        $decisions = $request->decisions;
        try {
            //code...
            DB::beginTransaction();
            Statustables::where('id_tabel', $data[0]['id_tabel'])
                ->where('tahun', $data[0]['tahun'])
                ->update([
                    'status' => ($decisions == "reject") ? '4' : '5',
                    'edited_by' => auth()->user()->id,
                ]);
            $status = Statustables::where('id_tabel', $data[0]['id_tabel'])
                ->where('tahun', $data[0]['tahun'])
                ->leftJoin('status_desc', 'statustables.status', '=', 'status_desc.id')
                ->first(['statustables.*', 'status_desc.label as statuslabel']);

            $isKominfo = auth()->user()->role == "kominfo";
            $Admins = ($isKominfo) ? "Kominfo" : "Admin";
            if ($status->status == '4') {
                # code...
                Notifikasi::create([
                    'id_statustabel' => $status->id,
                    'id_user' => auth()->user()->id,
                    'komentar' => $Admins . " telah me-reject data (perlu perbaikan) dengan judul ",
                ]);
            } elseif ($status->status == '5') {
                # code...
                Notifikasi::create([
                    'id_statustabel' => $status->id,
                    'id_user' => auth()->user()->id,
                    'komentar' => $Admins . " telah me-finalkan data dengan judul ",
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();

            // Handle the exception (log it, show a user-friendly message, etc.)
            // For example, you can log the error:
            return response()->json($th->getMessage());
        }

        return redirect()->route('tabel.entri', ['id' => $status->id])->with('message', 'Berhasil');
    }

    public function statusDestroy(Request $request)
    {
        $thisStatus = Statustables::where('id', $request->id);
        $thisStatus->update(['status' => '6']);
        return redirect()->route('tabel.index')->with('message', 'Berhasil menghapus tabel tahun tersebut');
    }
}
