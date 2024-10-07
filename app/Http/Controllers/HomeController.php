<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\ColumnOrder;
use App\Models\DataCategory;
use App\Models\Datacontent;
use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\MetadataVariabel;
use App\Models\Notifikasi;
use App\Models\Row;
use App\Models\RowGroup;
use App\Models\RowOrder;
use App\Models\Statustables;
use App\Models\Subject;
use App\Models\Tabel;
use App\Models\Turtahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        //
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;
        $query = Statustables::query();
        $dataToCounted = $query
            ->where('status', 5)
            ->join('tabels', 'statustables.id_tabel', '=', 'tabels.id')
            ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
            ->join('master_wilayah', 'dinas.wilayah_fullcode', '=', 'master_wilayah.wilayah_fullcode')
            ->join('subjects', 'tabels.id_subjek', '=', 'subjects.id')
            ->leftJoin('klasifikasi_tabel', 'tabels.id', '=', 'klasifikasi_tabel.id_tabel')
            ->orderBy('statustables.tahun', 'desc')
            ->orderBy('statustables.updated_at', 'desc')
            ->select([
                'statustables.id as id_statustables',
                'statustables.tahun',
                'tabels.*',
                'dinas.id as id_dinas',
                'dinas.nama as nama_dinas',
                'master_wilayah.wilayah_fullcode as kode_wilayah',
                'master_wilayah.label as nama_regions',
                'subjects.id as id_subjects',
                'subjects.label as nama_subjects',
                'statustables.updated_at as status_updated',
                'klasifikasi_tabel.id_category as kategori'
            ]);

        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['tahun'])) {
                $filter['tahun'] = array_values(array_filter($filter['tahun'], function ($value) {
                    return $value !== 'all';
                }));
                if (!empty($filter['tahun']))
                    $query->whereIn('statustables.tahun', $filter['tahun']);
            }
            if (!empty($filter['kode']))
                $query->whereIn('master_wilayah.wilayah_fullcode', $filter['kode']);
            if (!empty($filter['dinas'])) {
                $filter['dinas'] = array_values(array_filter($filter['dinas'], function ($value) {
                    return $value !== 'all';
                }));
                if (!empty($filter['dinas']))
                    $query->whereIn('dinas.id', $filter['dinas']);
            }
            if (!empty($filter['subjek']))
                $query->whereIn('subjects.id', $filter['subjek']);
            if (!empty($filter['label'])) {
                $query
                    // ->where('master_wilayah.label', 'like', '%' . $filter['label'] . '%')
                    // ->orWhere('statustables.tahun', 'like', '%' . $filter['label'] . '%')
                    // ->orWhere('dinas.nama', 'like', '%' . $filter['label'] . '%')
                    // ->orWhere('subjects.label', 'like', '%' . $filter['label'] . '%')
                    ->where('tabels.label', 'like', '%' . $filter['label'] . '%')
                    // ->orWhere('statustables.updated_at', 'like', '%' . $filter['label'] . '%')
                ;
            }
            if (!empty($filter['category'])) {
                if (!in_array(0, $filter['category'], true)) {
                    $query->whereIn('klasifikasi_tabel.id_category', $filter['category']);
                }
            }
        }
        $countData = $dataToCounted->count();
        $tabels = $query->paginate($paginated, ['*'], 'page', $currentPage);
        $dinas = [];
        $tempt_dinas = [];
        $provs = [];
        $kabs = [];
        $kecs = [];
        $desa = [];
        $subjects = [];

        $getIDtabel = Statustables::where('status', 5)->distinct()->pluck('id_tabel');
        $getIDDinas = Tabel::whereIn('id', $getIDtabel)->distinct()->pluck('id_dinas');
        $dinasUsed = Dinas::whereIn('id', $getIDDinas)
            ->join('master_wilayah as mw', 'mw.wilayah_fullcode', '=', 'dinas.wilayah_fullcode')
            ->select(['dinas.*', 'mw.label as label_region'])->get();
        $getIDSubject = Tabel::whereIn('id', $getIDtabel)->distinct()->pluck('id_subjek');
        $subjects = Subject::whereIn('id', $getIDSubject)->get();
        foreach ($dinasUsed as $key => $value) {
            # code...
            if (!isset($tempt_dinas[$value->id])) {
                $tempt_dinas[$value->id] = [
                    'value' => $value->id,
                    'label' => $value->nama,
                ];
            }
            $text = $value->label_region;
            $partOfText = explode(' ', $text);
            array_shift($partOfText);
            $modifiedText = implode(' ', $partOfText);

            $kode = $value->wilayah_fullcode;
            $kabupaten_kode = substr($kode, 2, 2);
            $kecamatan_kode = substr($kode, 4, 3);
            $desa_kode = substr($kode, 7, 3);
            if ($kabupaten_kode != '00') {
                $kab_label = MasterWilayah::where('kab', 'like', $kabupaten_kode)
                    ->where('kec', 'like', '000')->value('label');
                $partOfText = explode(' ', $kab_label);
                array_shift($partOfText);
                $modifiedKabLabel = implode(' ', $partOfText);
                $kabs[] = [
                    'label' => $modifiedKabLabel,
                    'wilayah_fullcode' => MasterWilayah::where('kab', 'like', $kabupaten_kode)
                        ->where('kec', 'like', '000')->value('wilayah_fullcode')
                ];

                if ($kecamatan_kode != '000') {
                    $kec_label = MasterWilayah::where('kab', 'like', $kabupaten_kode)
                        ->where('kec', 'like', $kecamatan_kode)->value('label');
                    $partOfText = explode(' ', $kec_label);
                    array_shift($partOfText);
                    $modifiedKecLabel = implode(' ', $partOfText);
                    $kecs[] = [
                        'label' => $modifiedKecLabel,
                        'parent_code' => $kabupaten_kode,
                        'wilayah_fullcode' => MasterWilayah::where('kab', 'like', $kabupaten_kode)
                            ->where('kec', 'like', $kecamatan_kode)->value('wilayah_fullcode'),
                    ];

                    if ($desa_kode != '000') {
                        $desa[] = [
                            'label' => $modifiedText,
                            'parent_code' => $kabupaten_kode . $kecamatan_kode,
                            'wilayah_fullcode' => $kode,
                        ];
                    }
                }
            }
        }
        $provs[] = [
            'label' => 'SULAWESI UTARA',
            'wilayah_fullcode' => '7100000000'
        ];
        $kabs = array_values(array_unique($kabs, SORT_REGULAR));
        $kecs = array_values(array_unique($kecs, SORT_REGULAR));
        $desa = array_values(array_unique($desa, SORT_REGULAR));
        $wilayahs = (sizeof($dataToCounted->get()) > 0) ? array_merge($provs, $kabs) : [];
        $tahuns = Statustables::where('status', 5)
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->get(['tahun as value', 'tahun as label']);
        $countfinals = Statustables::where('status', 5)->count();
        $dinas = array_values($tempt_dinas);
        $category = DataCategory::get(['id as value', 'label as label']);

        if ($request->paginated) {
            return response()->json([
                'countTabels' => $countData,
                'tabels' => $tabels,
            ]);
        }
        return Inertia::render('Home/Home', [
            'kecs' => $this->sortHome($kecs),
            'desa' => $this->sortHome($desa),
            'wilayahs' => $this->sortHome($wilayahs),
            'dinas' => $dinas,
            'tabels' => $tabels,
            'subjects' => $subjects,
            'category' => $category,
            // 'counttabels' => $counttabels,
            'counttabels' => $countData,
            'countfinals' => $countfinals,
            'tahuns' => $tahuns,
        ]);
    }

    private function sortHome($array)
    {
        usort($array, function ($a, $b) {
            if ($a['wilayah_fullcode'] === $b['wilayah_fullcode']) {
                return 0;
            }

            // Handle null values: move them to the end of the array
            if ($a['wilayah_fullcode'] === null) {
                return 1;
            }
            if ($b['wilayah_fullcode'] === null) {
                return -1;
            }

            // Compare the wilayah_fullcode values
            return $a['wilayah_fullcode'] <=> $b['wilayah_fullcode'];
        });
        return $array;
    }

    public function monitoring(Request $request)
    {
        $ourDinas = Dinas::whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->pluck('id');
        $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;

        $query = Statustables::query();
        if ($request->orderAttribute) {
            $order = $request->orderAttribute;
            if (sizeof($order) > 2) {
                $query->orderBy($order['label'], $order['value']);
            }
        }
        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['nama_dinas'])) {
                $query->where('d.nama', 'like', '%' . $filter['nama_dinas'] . '%');
            }
            if (!empty($filter['years'])) {
                $query->where('statustables.tahun', $filter['years']);
            }
            if (!empty($filter['wilayah'])) {
                $query->where('d.wilayah_fullcode', $filter['wilayah']);
            }
        }
        $this_monitoring = $query->join('tabels as t', 't.id', '=', 'statustables.id_tabel')
            ->join('dinas as d', 'd.id', '=', 't.id_dinas')
            ->select(
                'd.nama as nama_dinas',
                DB::raw('count(case when statustables.status = 1 then 1 end) as jumlah_satu'),
                DB::raw('count(case when statustables.status = 2 then 1 end) as jumlah_dua'),
                DB::raw('count(case when statustables.status = 3 then 1 end) as jumlah_tiga'),
                DB::raw('count(case when statustables.status = 4 then 1 end) as jumlah_empat'),
                DB::raw('count(case when statustables.status = 5 then 1 end) as jumlah_lima'),
                DB::raw('count(case when statustables.status = 6 then 1 end) as jumlah_enam')
            )
            ->whereIn('t.id_dinas', $ourDinas)
            ->groupBy('d.nama');

        $countData = $this_monitoring->get()->count();
        $monitoring = $this_monitoring->paginate($paginated, ['*'], 'page', $currentPage);

        $years = Statustables::whereIn('id_tabel', $myTabels)->distinct()->orderBy('tahun', 'desc')->get(['tahun as label', 'tahun as value']);
        $wilayah = MasterWilayah::getMyWilayah();
        if ($request->paginated) {
            return response()->json([
                'countData' => $countData,
                'this_monitoring' => $monitoring,
            ]);
        }
        return Inertia::render('Home/Monitoring', [
            'this_monitoring' => $monitoring,
            'years' => $years,
            'kabs' => $wilayah['kabs'],
            'countData' => $countData
        ]);
    }

    public function dashboard(Request $request)
    {
        //check role
        if ($request->paginated)
            $paginated = $request->paginated;
        else
            $paginated = 10;
        if ($request->currentPage)
            $currentPage = $request->currentPage;
        else
            $currentPage = 1;
        $query = Notifikasi::query();
        if (auth()->user()->role != 'produsen') {
            $ourDinas = Dinas::whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->pluck('id');
            $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');
            $wilayah = MasterWilayah::getMyWilayah();
            $query->whereIn('d.wilayah_fullcode', MasterWilayah::getDinasWilayah());
        } else {
            $myDinas = auth()->user()->id_dinas;
            $myTabels = Tabel::where('id_dinas', $myDinas)->pluck('id');
        }

        $query->where('u.id', auth()->user()->id)
            ->join('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
            ->join('tabels as t', 't.id', '=', 's.id_tabel')
            ->join('dinas as d', 'd.id', '=', 't.id_dinas')
            ->join('users as u', 'u.id_dinas', '=', 'd.id')
            ->orderBy('notifikasi.created_at', 'desc')
            ->select([
                'notifikasi.*',
                'notifikasi.created_at as timestamp',
                't.label as judul_tabel',
                's.tahun as tahundata',
                's.status as status',
            ]);
        $notifikasiList = $query->paginate($paginated, ['*'], 'page', $currentPage);
        if ($request->paginated) {
            return response()->json([
                'notifikasiList' => $notifikasiList,
            ]);
        }

        #status 1
        $newTabels = Statustables::whereIn('id_tabel', $myTabels)->where('status', 1)->count();

        #status 2
        $entriTabels = Statustables::whereIn('id_tabel', $myTabels)->where('status', 2)->count();

        #status 3
        $verifyTabels = Statustables::whereIn('id_tabel', $myTabels)->where('status', 3)->count();

        #status 4
        $repairTabels = Statustables::whereIn('id_tabel', $myTabels)->where('status', 4)->count();

        #status 5
        $finalTabels = Statustables::whereIn('id_tabel', $myTabels)->where('status', 5)->count();

        #total tabel
        $totalTabels = $newTabels + $entriTabels + $verifyTabels + $repairTabels + $finalTabels;

        $years = Statustables::whereIn('id_tabel', $myTabels)->distinct()->orderBy('tahun')->get(['tahun as label', 'tahun as value']);


        // return view('dashboard', [
        // ]);

        return Inertia::render('Home/Dashboard', [
            'newTabels' => $newTabels,
            'pieValues' => [$finalTabels, $entriTabels, $verifyTabels, $repairTabels, $newTabels],
            'entriTabels' => $entriTabels,
            'verifyTabels' => $verifyTabels,
            'repairTabels' => $repairTabels,
            'finalTabels' => $finalTabels,
            'totalTabels' => $totalTabels,
            'notifikasiList' => $notifikasiList,
            'years' => $years,
            'wilayah' => (auth()->user()->role == 'admin') ? $wilayah["kabs"] : [],
        ]);
    }

    public function getDashboard(string $years, string $wilayah)
    {

        if (auth()->user()->role != 'produsen') {
            # code...
            $id_wilayah = MasterWilayah::getMyWilayahId();
            $ourDinas = Dinas::whereIn('wilayah_fullcode', ($wilayah != "all") ? ((!$wilayah) ? MasterWilayah::getDinasWilayah() : [$wilayah]) : MasterWilayah::getDinasWilayah())
                ->pluck('id');
            $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');
            // dd($id_wilayah);
            $notifikasiList = Notifikasi::where('notifikasi.id_user', '!=', auth()->user()->id)
                ->whereIn('d.wilayah_fullcode', MasterWilayah::getDinasWilayah())
                ->leftJoin('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
                ->leftJoin('tabels as t', 't.id', '=', 's.id_tabel')
                ->leftJoin('dinas as d', 'd.id', '=', 't.id_dinas')
                ->leftJoin('users as u', 'u.id_dinas', '=', 'd.id')
                ->orderBy('notifikasi.created_at', 'desc')
                ->get([
                    'notifikasi.*',
                    't.label as judul_tabel',
                    's.tahun as tahundata',
                    's.status as status'
                ]);
            // dd($notifikasiList[0]->tahundata);
        } else {
            $myDinas = auth()->user()->id_dinas;
            $myTabels = Tabel::where('id_dinas', $myDinas)->pluck('id');
            // dd($myTabels);
            $notifikasiList = Notifikasi::where('u.id', auth()->user()->id)
                ->leftJoin('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
                ->leftJoin('tabels as t', 't.id', '=', 's.id_tabel')
                ->leftJoin('dinas as d', 'd.id', '=', 't.id_dinas')
                ->leftJoin('users as u', 'u.id_dinas', '=', 'd.id')
                ->orderBy('notifikasi.created_at', 'desc')
                ->get([
                    'notifikasi.*',
                    't.label as judul_tabel',
                    's.tahun as tahundata',
                    's.status as status',
                ]);
        }

        $years_all = Statustables::whereIn('id_tabel', $myTabels)->distinct()->orderBy('tahun')->pluck('tahun');
        #status 1
        $newTabels = Statustables::whereIn('id_tabel', $myTabels)
            ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
            ->where('status', 1)->count();
        // dd($newTabels);


        #status 2
        $entriTabels = Statustables::whereIn('id_tabel', $myTabels)
            ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
            ->where('status', 2)->count();

        #status 3
        $verifyTabels = Statustables::whereIn('id_tabel', $myTabels)
            ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
            ->where('status', 3)->count();

        #status 4
        $repairTabels = Statustables::whereIn('id_tabel', $myTabels)
            ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
            ->where('status', 4)->count();

        #status 5
        $finalTabels = Statustables::whereIn('id_tabel', $myTabels)
            ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
            ->where('status', 5)->count();

        #total tabel
        $totalTabels = $newTabels + $entriTabels + $verifyTabels + $repairTabels + $finalTabels;

        return response()->json([
            'newTabels' => $newTabels,
            'pieValues' => [$finalTabels, $entriTabels, $verifyTabels, $repairTabels, $newTabels],
            'entriTabels' => $entriTabels,
            'verifyTabels' => $verifyTabels,
            'repairTabels' => $repairTabels,
            'finalTabels' => $finalTabels,
            'totalTabels' => $totalTabels,
        ]);
    }

    public function show(Request $request)
    {

        $statusTabel = Statustables::join('tabels as t', 'statustables.id_tabel', 't.id')
            ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
            ->select(
                't.id as id_tabel',
                't.nomor as nomor',
                't.label as judul_tabel',
                'statustables.tahun',
                'sdesc.label as status',
                'statustables.id as id_statustables',
                'statustables.updated_at as status_updated'
            )
            ->where('statustables.id', $request->id)->first();

        if ($statusTabel->status != 'Final') {
            return response()->json([
                'message' => 'Data status tabel ini belum dalam status Final',
            ], 403);
        }
        $id_tabel = $statusTabel->id_tabel;
        $tahun = $statusTabel->tahun;

        $datacontents = Datacontent::where('id_tabel', $id_tabel)->where('tahun', $tahun)->get();
        $id_rows = [];
        $wilayah_fullcodes = [];
        $id_columns = [];
        $tahuns = Statustables::where('id_tabel', $id_tabel)
            ->where('status', 5)
            ->where('tahun', '!=', $tahun)
            ->pluck('tahun')->toArray();
        $turTahunKeys = [];

        foreach ($datacontents as $datacontent) {
            array_push($id_rows, $datacontent->id_row);
            array_push($id_columns, $datacontent->id_column);
            // array_push($tahuns, $datacontent->tahun);
            array_push($turTahunKeys, $datacontent->id_turtahun);

            array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
        }
        $tabels = Tabel::where('tabels.id', $id_tabel)
            ->leftJoin('subjects as sb', 'sb.id', '=', 'tabels.id_subjek')
            ->leftJoin('dinas as d', 'd.id', '=', 'tabels.id_dinas')
            ->leftJoin('master_wilayah as mw', 'mw.wilayah_fullcode', '=', 'd.wilayah_fullcode')
            ->first(['tabels.*', 'sb.label as subject_label', 'd.nama as dinas_label', 'mw.label as wilayah_label']);

        $rows = Row::whereIn('id', $id_rows)->get();
        $rowLabel = RowGroup::where('id', $rows[0]->id_row_groups)->get();
        $RowOrders = RowOrder::where('id_statustabel', $request->id)->value('orders');
        try {
            //code...
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
                if ($RowOrders)
                    $rows = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
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
                        if ($key == sizeof($tempt) - 1)
                            $text .= $value;
                        else
                            $text .= $value . ' - ';
                    }
                    $rowLabel = $text;
                } else
                    $rowLabel = RowGroup::where('id', $rows[0]->id_row_groups)->pluck('label')[0];
            }
        } catch (\Exception $e) {
            return response()->json(array('error' => $e->getMessage(), 'rows' => $rows));
        }
        //call the orders
        $ColumnOrders = ColumnOrder::where('id_statustabel', $request->id)->value('orders');
        if ($ColumnOrders) {
            $columns = Column::whereIn('id', $id_columns)->orderByRaw("FIELD(id," . $ColumnOrders . ")")->get();
        } else {
            $columns = Column::whereIn('id', $id_columns)->get();
        }
        if (!$rows[0]->id == 0 && $RowOrders)
            $rows = Row::whereIn('id', $id_rows)->orderByRaw("FIELD(id," . $RowOrders . ")")->get();

        $tahuns = array_unique($tahuns);
        sort($tahuns);
        $turtahuns = Turtahun::whereIn('id', $turTahunKeys)->get();
        $this_metavar = MetadataVariabel::where('id_tabel', $id_tabel)->get();
        foreach ($this_metavar as $key => $value) {
            # code...
            $value->number = $key + 1;
            $value->satuan = $tabels->unit;
        }
        return Inertia::render('Home/View', [
            'datacontents' => $datacontents,
            'tabels' => $tabels,
            'tahuns' => $tahuns,
            'tahun' => $tahun,
            'rows' => $rows,
            'row_label' => $rowLabel,
            'columns' => $columns,
            'turtahuns' => $turtahuns,
            'tabel' => $statusTabel,
            'metavars' => $this_metavar,
        ]);
    }

    public function fetch(Request $request)
    {
        $id_tabel = $request->id_tabel;
        $tahun = $request->tahun;
        $id_statustabel = $request->id_statustabel;
        $current = $request->current;
        // dd($tahun);
        $bigData = [];
        foreach ($tahun as $key => $value) {
            # code...
            $turTahunKeys = [];
            $id_rows = [];
            $wilayah_fullcodes = [];
            $id_columns = [];
            $bigData[$value]['data'] = Datacontent::where('id_tabel', $id_tabel)->where('tahun', $value)->get();
            foreach ($bigData[$value]['data'] as $datacontent) {
                array_push($id_rows, $datacontent->id_row);
                array_push($id_columns, $datacontent->id_column);
                array_push($turTahunKeys, $datacontent->id_turtahun);
                array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
            }
            // dd($turTahunKeys);
            $rows = Row::whereIn('id', $id_rows)->get();
            $RowOrders = RowOrder::where('id_statustabel', $id_statustabel)->value('orders');

            if ($rows[0]->id == 0) {
                $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                    ->orderByRaw("CASE WHEN desa = '000' THEN 1 ELSE 0 END")
                    ->orderBy('desa')
                    ->get();
                $rows = $temp;
                $kec = substr($wilayah_fullcodes[0], 4, 3);
                $kab = substr($wilayah_fullcodes[0], 2, 2);
                if ($kec != '000') {
                    $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                        ->orderByRaw("CASE WHEN kec = '000' THEN 1 ELSE 0 END")
                        ->orderBy('desa')
                        ->get();
                    $rows = $temp;
                } else if ($kab != '00') {
                    $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                        ->orderByRaw("CASE WHEN kab = '00' THEN 1 ELSE 0 END")
                        ->orderBy('desa')
                        ->get();
                    $rows = $temp;
                }
                if ($RowOrders)
                    $rows = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
                        ->orderByRaw("FIELD(wilayah_fullcode," . $RowOrders . ")")->get();
            }

            //call the orders
            $ColumnOrders = ColumnOrder::where('id_statustabel', $id_statustabel)->value('orders');
            if ($ColumnOrders) {
                $columns = Column::whereIn('id', $id_columns)->orderByRaw("FIELD(id," . $ColumnOrders . ")")->get();
            } else {
                $columns = Column::whereIn('id', $id_columns)->get();
            }
            if (!$rows[0]->id == 0 && $RowOrders)
                $rows = Row::whereIn('id', $id_rows)->orderByRaw("FIELD(id," . $RowOrders . ")")->get();

            $bigData[$value]['turtahun'] = Turtahun::whereIn('id', $turTahunKeys)->get();
            $bigData[$value]['tahun'] = $value;
            $bigData[$value]['rows'] = $rows;
            $bigData[$value]['columns'] = $columns;
        }
        // dd($bigData);
        return response()->json($bigData);
    }

    public function lineChart(Request $request)
    {
        $id_column = $request->id_column;
        $id_row = $request->id_row;
        $tahun = $request->tahun;
        $id_tabel = $request->id_tabel;

        $result = [];
        foreach ($id_row as $key => $value) {
            # code...
            $split_value = explode('-', $value);
            // dd($split_value);

            // Determine the column to filter by based on the request
            if ($split_value[0] == '0')
                $column = 'wilayah_fullcode';
            else
                $column = 'id_row';
            // $column = $request->wilayah_fullcode ? 'wilayah_fullcode' : 'id_row';

            // Fetch the data based on the determined column
            $data = Datacontent::where('id_column', $id_column)
                ->where($column, $split_value[1])
                ->where('id_tabel', $id_tabel)
                ->whereIn('tahun', $tahun)
                ->orderBy('tahun')
                ->get();

            $result[$key]['label'] = ($split_value[0] == '0') ? MasterWilayah::where('wilayah_fullcode', $split_value[1])->value('label') :
                Row::where('id', $split_value[1])->value('label');
            $result[$key]['backgroundColor'] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            $result[$key]['data'] = $data->pluck('value')->toArray();
            foreach ($result[$key]['data'] as $keyInside => $valueInside) {
                # code...
                $splitdata = explode(',', $valueInside);
                // dd($splitdata);
                //left-side
                $removePoint = str_replace('.', '', $splitdata[0]);
                // If there is a decimal part (right side of the comma), rejoin the parts
                if (isset($splitdata[1])) {
                    $result[$key]['data'][$keyInside] = implode('.', [$removePoint, $splitdata[1]]);
                } else {
                    // If there's no decimal part, just assign the integer part
                    $result[$key]['data'][$keyInside] = $removePoint;
                }
            }
        }
        return response()->json($result);
    }
}
