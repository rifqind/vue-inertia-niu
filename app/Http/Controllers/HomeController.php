<?php

namespace App\Http\Controllers;

use App\Models\Column;
use App\Models\Datacontent;
use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\MetadataVariabel;
use App\Models\Notifikasi;
use App\Models\Region;
use App\Models\Row;
use App\Models\RowGroup;
use App\Models\Rowlabel;
use App\Models\Statustables;
use App\Models\Subject;
use App\Models\Tabel;
use App\Models\Turtahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    //     /**
    //      * Display a listing of the resource.
    //      */
    // public function index()
    // {
    //     return Inertia::render('Home/Home');
    // }
    public function index()
    {
        //
        $tabels = Statustables::where('status', 5)
            ->leftJoin('tabels', 'statustables.id_tabel', '=', 'tabels.id')
            ->leftJoin('dinas', 'tabels.id_dinas', '=', 'dinas.id')
            ->leftJoin('master_wilayah', 'dinas.wilayah_fullcode', '=', 'master_wilayah.wilayah_fullcode')
            ->leftJoin('subjects', 'tabels.id_subjek', '=', 'subjects.id')
            ->get([
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
            ]);
        $dinas = [];
        $provs = [];
        $kabs = [];
        $kecs = [];
        $desa = [];
        $subjects = [];
        foreach ($tabels as $tabel) {
            # code...
            $dinas[] = [
                'value' => $tabel->id_dinas,
                'label' => $tabel->nama_dinas,
            ];
            $text = $tabel->nama_regions;
            $partOfText = explode(' ', $text);
            array_shift($partOfText);
            $modifiedText = implode(' ', $partOfText);

            $kode = $tabel->kode_wilayah;
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
            $subjects[] = [
                'id' => $tabel->id_subjects,
                'label' => $tabel->nama_subjects,
            ];
        }
        $provs[] = [
            'label' => 'SULAWESI UTARA',
            'wilayah_fullcode' => '7100000000'
        ];
        $kabs = array_values(array_unique($kabs, SORT_REGULAR));
        $kecs = array_values(array_unique($kecs, SORT_REGULAR));
        $desa = array_values(array_unique($desa, SORT_REGULAR));
        $wilayahs = (sizeof($tabels) > 0) ? array_merge($provs, $kabs) : [];
        $subjects = array_values(array_unique($subjects, SORT_REGULAR));
        // $subjects = Subject::all();
        $tahuns = Statustables::where('status', 5)->distinct()->get(['tahun as value', 'tahun as label']);
        $countfinals = Statustables::where('status', 5)->count();
        $counttabels = $tabels->count();
        return Inertia::render('Home/Home', [
            // 'kabs' => $kabs,
            'kecs' => $kecs,
            'desa' => $desa,
            'wilayahs' => $wilayahs,
            'dinas' => $dinas,
            'tabels' => $tabels,
            'subjects' => $subjects,
            'counttabels' => $counttabels,
            'countfinals' => $countfinals,
            'tahuns' => $tahuns,
        ]);
    }


    public function monitoring()
    {
        $ourDinas = Dinas::whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->pluck('id');
        $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');

        $this_monitoring = DB::table('statustables as s')
            ->join('tabels as t', 't.id', '=', 's.id_tabel')
            ->join('dinas as d', 'd.id', '=', 't.id_dinas')
            ->select(
                'd.nama as nama_dinas',
                DB::raw('count(case when s.status = 1 then 1 end) as jumlah_satu'),
                DB::raw('count(case when s.status = 2 then 1 end) as jumlah_dua'),
                DB::raw('count(case when s.status = 3 then 1 end) as jumlah_tiga'),
                DB::raw('count(case when s.status = 4 then 1 end) as jumlah_empat'),
                DB::raw('count(case when s.status = 5 then 1 end) as jumlah_lima'),
                DB::raw('count(case when s.status = 6 then 1 end) as jumlah_enam')
            )
            ->whereIn('t.id_dinas', $ourDinas)
            ->groupBy('nama_dinas')
            ->get();

        $years = Statustables::whereIn('id_tabel', $myTabels)->distinct()->orderBy('tahun', 'desc')->get(['tahun as label', 'tahun as value']);

        return Inertia::render('Home/Monitoring', [
            'this_monitoring' => $this_monitoring,
            'years' => $years,
        ]);
    }

    public function getMonitoring(string $years)
    {
        $ourDinas = Dinas::whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->pluck('id');
        $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');
        $years_all = Statustables::whereIn('id_tabel', $myTabels)->distinct()->orderBy('tahun')->pluck('tahun');

        $this_monitoring = DB::table('statustables as s')
            ->join('tabels as t', 't.id', '=', 's.id_tabel')
            ->join('dinas as d', 'd.id', '=', 't.id_dinas')
            ->select(
                'd.nama as nama_dinas',
                DB::raw('count(case when s.status = 1 then 1 end) as jumlah_satu'),
                DB::raw('count(case when s.status = 2 then 1 end) as jumlah_dua'),
                DB::raw('count(case when s.status = 3 then 1 end) as jumlah_tiga'),
                DB::raw('count(case when s.status = 4 then 1 end) as jumlah_empat'),
                DB::raw('count(case when s.status = 5 then 1 end) as jumlah_lima'),
                DB::raw('count(case when s.status = 6 then 1 end) as jumlah_enam')
            )
            ->whereIn('t.id_dinas', $ourDinas)
            ->whereIn('s.tahun', ($years == 'all') ? $years_all : [$years])
            ->groupBy('nama_dinas')
            ->get();

        return response()->json($this_monitoring);
    }

    //     /**
    //      * Show the form for creating a new resource.
    //      */
    //     public function create()
    //     {
    //         //
    //     }

    //     /**
    //      * Store a newly created resource in storage.
    //      */
    //     public function store(Request $request)
    //     {
    //         //
    //     }

    //     /**
    //      * Display the specified resource.
    //      */
    //     // public function show(string $id)
    //     // {
    //     //     //
    //     // }

    //     /**
    //      * Show the form for editing the specified resource.
    //      */
    //     public function edit(string $id)
    //     {
    //         //
    //     }

    //     /**
    //      * Update the specified resource in storage.
    //      */
    //     public function update(Request $request, string $id)
    //     {
    //         //
    //     }

    //     /**
    //      * Remove the specified resource from storage.
    //      */
    //     public function destroy(string $id)
    //     {
    //         //
    //     }

    public function dashboard()
    {
        //check role
        if (auth()->user()->role != 'produsen') {
            # code...
            $ourDinas = Dinas::whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->pluck('id');
            // dd($ourDinas);
            $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');

            $notifikasiList = Notifikasi::where('notifikasi.id_user', '!=', auth()->user()->id)
                ->whereIn('d.wilayah_fullcode', MasterWilayah::getDinasWilayah())
                ->leftJoin('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
                ->leftJoin('tabels as t', 't.id', '=', 's.id_tabel')
                ->leftJoin('dinas as d', 'd.id', '=', 't.id_dinas')
                ->leftJoin('users as u', 'u.id_dinas', '=', 'd.id')
                ->orderBy('notifikasi.created_at', 'desc')
                ->get([
                    'notifikasi.*',
                    'notifikasi.created_at as timestamp',
                    't.label as judul_tabel',
                    's.tahun as tahundata',
                    's.status as status',
                ]);
            $wilayah = MasterWilayah::getMyWilayah();
            // dd($notifikasiList[0]->tahundata);
        } else {
            $myDinas = auth()->user()->id_dinas;
            $myTabels = Tabel::where('id_dinas', $myDinas)->pluck('id');
            $notifikasiList = Notifikasi::where('u.id', auth()->user()->id)
                ->leftJoin('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
                ->leftJoin('tabels as t', 't.id', '=', 's.id_tabel')
                ->leftJoin('dinas as d', 'd.id', '=', 't.id_dinas')
                ->leftJoin('users as u', 'u.id_dinas', '=', 'd.id')
                ->orderBy('notifikasi.created_at', 'desc')
                ->get([
                    'notifikasi.*',
                    'notifikasi.created_at as timestamp',
                    't.label as judul_tabel',
                    's.tahun as tahundata',
                    's.status as status',
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
        // dd($years, $wilayah);

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

    //     public function getDatacontent(Request $request)
    //     {
    //         $tabel_id = $request->query('tabel_id');
    //         $data = Datacontent::where('label', 'LIKE', $tabel_id . '%')->get();
    //         $id_rows = [];
    //         $id_columns = [];
    //         foreach ($data as $dat) {
    //             $split = explode("-", $dat->label);
    //             array_push($id_rows, $split[1]);
    //             array_push($id_columns, $split[2]);
    //             $tahun = $split[3];
    //             $turtahuns = $split[4];
    //         }
    //         $tabels = Tabel::where('id', $tabel_id)->get();
    //         $rows = Row::whereIn('id', $id_rows)->get();
    //         $rowLabel = Rowlabel::where('id', $rows[0]->id_rowlabels)->get();
    //         $columns = Column::whereIn('id', $id_columns)->get();

    //         return response()->json([
    //             'datacontents' => $data,
    //             'tabels' => $tabels,
    //             'rows' => $rows,
    //             'row_label' => $rowLabel,
    //             'columns' => $columns,
    //             'tahun' => $tahun,
    //             'turtahuns' => $turtahuns,
    //         ]);
    //     }

    public function show(Request $request)
    {
        $statusTabel = Statustables::join('tabels as t', 'statustables.id_tabel', 't.id')
            ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
            ->select(
                't.id as id_tabel',
                't.label as judul_tabel',
                'statustables.tahun',
                'sdesc.label as status',
                'statustables.id as id_statustables',
                'statustables.updated_at as status_updated'
            )
            ->where('statustables.id', $request->id)->first();


        $id_tabel = $statusTabel->id_tabel;
        $tahun = $statusTabel->tahun;

        $datacontents = Datacontent::where('id_tabel', $id_tabel)->where('tahun', $tahun)->get();
        $id_rows = [];
        $wilayah_fullcodes = [];
        $id_columns = [];
        $tahuns = [];
        $turTahunKeys = [];

        foreach ($datacontents as $datacontent) {
            array_push($id_rows, $datacontent->id_row);
            array_push($id_columns, $datacontent->id_column);
            array_push($tahuns, $datacontent->tahun);
            array_push($turTahunKeys, $datacontent->id_turtahun);

            array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
        }
        $tabels = Tabel::where('tabels.id', $id_tabel)
            ->leftJoin('subjects as sb', 'sb.id', '=', 'tabels.id_subjek')
            ->leftJoin('dinas as d', 'd.id', '=', 'tabels.id_dinas')
            ->leftJoin('master_wilayah as mw', 'mw.wilayah_fullcode', '=', 'd.wilayah_fullcode')
            ->first(['tabels.*', 'sb.label as subject_label', 'd.nama as dinas_label', 'mw.label as wilayah_label']);

        $rows = Row::whereIn('id', $id_rows)->get();
        $rowLabel = RowGroup::where('id', $rows[0]->id_rowlabels)->get();
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
                $rowLabel = RowGroup::where('id', $rows[0]->id_rowlabels)->pluck('label')[0];
            }
        } catch (\Exception $e) {
            return response()->json(array('error' => $e->getMessage(), 'rows' => $rows));
        }

        $columns = Column::whereIn('id', $id_columns)->get();
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
}
