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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class TabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $this_dinas = auth()->user()->id_dinas;
        $this_role = auth()->user()->role;
        $routeName = Route::currentRouteName();
        if ($this_role == 'produsen') {
            # code...
            $tables = Statustables::where('dinas.id', $this_dinas)
                ->where('statustables.status', ($routeName != 'tabel.deletedList') ? '<' : '=', '6')
                ->join('tabels', 'statustables.id_tabel', '=', 'tabels.id')
                ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
                ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
                ->get(
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
        } else {
            $tables = Statustables::whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah())
                ->where('statustables.status', ($routeName != 'tabel.deletedList') ? '<' : '=', '6')
                ->join('tabels', 'statustables.id_tabel', '=', 'tabels.id')
                ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
                ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
                ->get(
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
        }
        $table_objects = [];
        foreach ($tables as $key => $table) {
            $datacontents = Datacontent::where('id_tabel', $table->tabelUuid)->get();
            $id_rows = [];
            $id_columns = [];
            $wilayah_fullcodes = [];
            foreach ($datacontents as $datacontent) {
                $split = explode("-", $datacontent->label);
                array_push($id_rows, $datacontent->id_row);
                array_push($id_columns, $datacontent->id_column);
                array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
            }
            $yearList = Statustables::where('id_tabel', $table->tabelUuid)->distinct()->get(['statustables.tahun']);
            $rows = Row::whereIn('id', $id_rows)->get();
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
            $columns = Column::whereIn('id', $id_columns)->get('label');
            $who_updated = User::where('id', $table->edited_by)->value('username');
            $when_updated = $table->status_updated;
            $NumberAndLabel = $table->nomor . ' - ' . $table->label;
            array_push($table_objects, [
                'number' => $key + 1,
                // 'label' => $table->label,
                'label' => $NumberAndLabel,
                'nama_dinas' => $table->nama_dinas,
                'id' => $table->tabelUuid,
                'rows' => $rows,
                'row_label' => $rowLabel,
                'columns' => $columns,
                'tahun' => $table->tahun,
                'yearList' => $yearList,
                'status' => $table->status,
                'id_statustables' => $table->id_statustables,
                'status_updated' => $when_updated,
                'who_updated' => $who_updated,
            ]);
        }

        return Inertia::render('Tabel/Index', [
            'tables' => $table_objects,
            'role' => $this_role,
        ]);
    }

    public function master()
    {
        //
        $tables = Tabel::leftJoin('dinas', 'dinas.id', '=', 'tabels.id_dinas')
            ->whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah())
            ->get(['tabels.*', 'tabels.id as tabelUuid']);
        $table_objects = [];
        $number = 1;
        foreach ($tables as $key => $table) {
            $datacontents = Datacontent::where('id_tabel', $table->id)->get();
            $id_rows = [];
            $tahunObjects = Statustables::where('id_tabel', $table->id)->select('tahun')->distinct()->get();

            $tahuns = $tahunObjects->pluck('tahun')->toArray();
            $id_columns = [];
            $wilayah_fullcodes = [];
            foreach ($datacontents as $datacontent) {
                array_push($id_rows, $datacontent->id_row);
                array_push($id_columns, $datacontent->id_column);
                array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
            }
            $rows = Row::whereIn('id', $id_rows)->get();
            try {
                //code...
                if ($rows[0]->id == 0) {
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
                return response()->json(array('error' => $e->getMessage(), 'tersangka' => $table->id, 'rows' => $rows));
            }
            $columns = Column::whereIn('id', $id_columns)->get();
            array_push($table_objects, [
                'number' => $number ++,
                'label' => $table->label,
                'id' => $table->tabelUuid,
                'nama_dinas' => $table->dinas->nama,
                'rows' => $rows,
                'row_label' => $rowLabel,
                'columns' => $columns,
                'tahuns' => $tahuns,
                'status' => $table->status,
                'id_statustables' => $table->id_statustables,
            ]);
        }
        return Inertia::render('Master/Tabel', [
            'tables' => $table_objects,
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

        $oldDataContents = Datacontent::where('id_tabel', $firstStatus->id_tabel)
            ->where('tahun', $firstStatus->tahun)
            ->get();

        $newDataContents = [];
        foreach ($oldDataContents as $record) {
            array_push(
                $newDataContents,
                [
                    'value' => '',
                    'id_tabel' => $record->id_tabel,
                    'id_row' => $record->id_row,
                    'id_column' => $record->id_column,
                    'id_turtahun' => $record->id_turtahun,
                    'tahun' => $request->tahun,
                    'wilayah_fullcode' => $record->wilayah_fullcode,
                ]
            );
        }
        // add new record in statustabel 
        try {
            DB::beginTransaction();
            $newStatus = Statustables::create([
                'id_tabel' => $request->id,
                'tahun' => $request->tahun,
                'status' => '1',
                'edited_by' => auth()->user()->id,
            ]);
            Datacontent::insert($newDataContents);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
        return redirect()->route('tabel.master')->with('message', 'Berhasil Menyalin Tabel !');
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
