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
    public function index()
    {
        return Inertia::render('Home/Home');
    }
    //     public function index()
    //     {
    //         //
    //         $tabels = Statustables::where('status', 5)
    //             ->leftJoin('tabels', 'statustables.id_tabel', '=', 'tabels.id')
    //             ->leftJoin('dinas', 'tabels.id_dinas', '=', 'dinas.id')
    //             ->leftJoin('master_wilayah', 'dinas.wilayah_fullcode', '=', 'master_wilayah.wilayah_fullcode')
    //             ->leftJoin('subjects', 'tabels.id_subjek', '=', 'subjects.id')
    //             ->get([
    //                 'statustables.id as id_statustables',
    //                 'statustables.tahun',
    //                 'tabels.*',
    //                 'dinas.id as id_dinas',
    //                 'dinas.nama as nama_dinas',
    //                 'master_wilayah.wilayah_fullcode as kode_wilayah',
    //                 'master_wilayah.label as nama_regions',
    //                 'subjects.id as id_subjects',
    //                 'subjects.label as nama_subjects',
    //                 'statustables.updated_at as status_updated',
    //             ]);
    //         $dinas = [];
    //         $provs = [];
    //         $kabs = [];
    //         $kecs = [];
    //         $desa = [];
    //         $subjects = [];
    //         foreach ($tabels as $tabel) {
    //             # code...
    //             $dinas[] = [
    //                 'id' => $tabel->id_dinas,
    //                 'nama' => $tabel->nama_dinas,
    //             ];
    //             $text = $tabel->nama_regions;
    //             $partOfText = explode(' ', $text);
    //             array_shift($partOfText);
    //             $modifiedText = implode(' ', $partOfText);

    //             $kode = $tabel->kode_wilayah;
    //             $kabupaten_kode = substr($kode, 2, 2);
    //             $kecamatan_kode = substr($kode, 4, 3);
    //             $desa_kode = substr($kode, 7, 3);
    //             if ($kabupaten_kode != '00') {
    //                 $kab_label = MasterWilayah::where('kab', 'like', $kabupaten_kode)
    //                     ->where('kec', 'like', '000')->value('label');
    //                 $partOfText = explode(' ', $kab_label);
    //                 array_shift($partOfText);
    //                 $modifiedKabLabel = implode(' ', $partOfText);
    //                 $kabs[] = [
    //                     'label' => $modifiedKabLabel,
    //                     'wilayah_fullcode' => MasterWilayah::where('kab', 'like', $kabupaten_kode)
    //                         ->where('kec', 'like', '000')->value('wilayah_fullcode')
    //                 ];

    //                 if ($kecamatan_kode != '000') {
    //                     $kec_label = MasterWilayah::where('kab', 'like', $kabupaten_kode)
    //                         ->where('kec', 'like', $kecamatan_kode)->value('label');
    //                     $partOfText = explode(' ', $kec_label);
    //                     array_shift($partOfText);
    //                     $modifiedKecLabel = implode(' ', $partOfText);
    //                     $kecs[] = [
    //                         'label' => $modifiedKecLabel,
    //                         'parent_code' => $kabupaten_kode,
    //                         'wilayah_fullcode' => MasterWilayah::where('kab', 'like', $kabupaten_kode)
    //                             ->where('kec', 'like', $kecamatan_kode)->value('wilayah_fullcode'),
    //                     ];

    //                     if ($desa_kode != '000') {
    //                         $desa[] = [
    //                             'label' => $modifiedText,
    //                             'parent_code' => $kabupaten_kode.$kecamatan_kode,
    //                             'wilayah_fullcode' => $kode,
    //                         ];
    //                     }
    //                 }
    //             }
    //             $subjects[] = [
    //                 'id' => $tabel->id_subjects,
    //                 'label' => $tabel->nama_subjects,
    //             ];
    //         }
    //         $provs[] = [
    //             'label' => 'SULAWESI UTARA',
    //             'wilayah_fullcode' => '7100000000'
    //         ];
    //         $kabs = array_unique($kabs, SORT_REGULAR);
    //         $kecs = array_unique($kecs, SORT_REGULAR);
    //         $desa = array_unique($desa, SORT_REGULAR);
    //         $wilayahs = (sizeof($tabels) > 0) ? array_merge($provs, $kabs) : [];
    //         $subjects = array_unique($subjects, SORT_REGULAR);
    //         // $subjects = Subject::all();
    //         $tahuns = Statustables::where('status', 5)->distinct()->get('tahun');
    //         $countfinals = Statustables::where('status', 5)->count();
    //         $counttabels = $tabels->count();
    //         return view('frontpage', [
    //             // 'kabs' => $kabs,
    //             'kecs' => $kecs,
    //             'desa' => $desa,
    //             'wilayahs' => $wilayahs,
    //             'dinas' => $dinas,
    //             'tabels' => $tabels,
    //             'subjects' => $subjects,
    //             'counttabels' => $counttabels,
    //             'countfinals' => $countfinals,
    //             'tahuns' => $tahuns,
    //         ]);
    //     }

    //     public function getSearch(Request $request)
    //     {
    //         $kecs = $request->input('kecs', []);
    //         $desa = $request->input('desa', []);
    //         if ($kecs) {
    //             # code...
    //             if ($desa) {
    //                 # code...
    //                 $wilayah = $desa;
    //             } else {
    //                 $wilayah = $kecs;
    //             }
    //         } else {
    //             $wilayah = $request->input('wilayah', []);
    //         }

    //         $dinas = $request->input('dinas', []);
    //         $subject = $request->input('subject', []);
    //         $tahuns = $request->input('tahuns', []);

    //         $searchWord = $request->input('searchData');

    //         $tabels = Statustables::when(!empty($wilayah), function ($query) use ($wilayah) {
    //             $query->whereIn('master_wilayah.wilayah_fullcode', $wilayah);
    //         })
    //             ->when(!empty($dinas), function ($query) use ($dinas) {
    //                 $query->whereIn('dinas.id', $dinas);
    //             })
    //             ->when(!empty($subject), function ($query) use ($subject) {
    //                 $query->whereIn('subjects.id', $subject);
    //             })
    //             ->when(!empty($tahuns), function ($query) use ($tahuns) {
    //                 $query->whereIn('tahun', $tahuns);
    //             })
    //             ->when($searchWord, function ($query) use ($searchWord) {
    //                 $query->where(function ($query) use ($searchWord) {
    //                     $query->where('tabels.label', 'like', '%' . $searchWord . '%')
    //                         ->orWhere('dinas.nama', 'like', '%' . $searchWord . '%')
    //                         ->orWhere('master_wilayah.label', 'like', '%' . $searchWord . '%')
    //                         ->orWhere('subjects.label', 'like', '%' . $searchWord . '%');
    //                 });
    //             })
    //             ->where('status', 5)
    //             ->leftJoin('tabels', 'statustables.id_tabel', '=', 'tabels.id')
    //             ->leftJoin('dinas', 'tabels.id_dinas', '=', 'dinas.id')
    //             ->leftJoin('master_wilayah', 'dinas.wilayah_fullcode', '=', 'master_wilayah.wilayah_fullcode')
    //             ->leftJoin('subjects', 'tabels.id_subjek', '=', 'subjects.id')
    //             ->get([
    //                 'statustables.tahun',
    //                 'tabels.*',
    //                 'dinas.nama as nama_dinas',
    //                 'master_wilayah.label as nama_regions',
    //                 'subjects.label as nama_subjects',
    //                 'statustables.updated_at as status_updated',
    //             ]);


    //         $counttabels = $tabels->count();
    //         return view('tabel-list', compact('tabels', 'counttabels'))->render();
    //     }

    //     public function getDashboard(Request $request)
    //     {
    //         $wilayah = $request->input('wilayah');
    //         $years = $request->input('year');

    //         if (auth()->user()->role != 'produsen') {
    //             # code...
    //             $id_wilayah = MasterWilayah::getMyWilayahId();
    //             $ourDinas = Dinas::whereIn('wilayah_fullcode', ($wilayah != "all") ?  ((!$wilayah) ? MasterWilayah::getDinasWilayah() : [$wilayah]) : MasterWilayah::getDinasWilayah())
    //                 ->pluck('id');
    //             $myTabels = Tabel::whereIn('id_dinas', $ourDinas)->pluck('id');
    //             // dd($id_wilayah);
    //             $notifikasiList = Notifikasi::where('notifikasi.id_user', '!=', auth()->user()->id)
    //                 ->whereIn('d.wilayah_fullcode', MasterWilayah::getDinasWilayah())
    //                 ->leftJoin('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
    //                 ->leftJoin('tabels as t', 't.id', '=', 's.id_tabel')
    //                 ->leftJoin('dinas as d', 'd.id', '=', 't.id_dinas')
    //                 ->leftJoin('users as u', 'u.id_dinas', '=', 'd.id')
    //                 ->orderBy('notifikasi.created_at', 'desc')
    //                 ->get([
    //                     'notifikasi.*',
    //                     't.label as judul_tabel',
    //                     's.tahun as tahundata',
    //                     's.status as status'
    //                 ]);
    //             // dd($notifikasiList[0]->tahundata);
    //         } else {
    //             $myDinas = auth()->user()->id_dinas;
    //             $myTabels = Tabel::where('id_dinas', $myDinas)->pluck('id');
    //             // dd($myTabels);
    //             $notifikasiList = Notifikasi::where('u.id', auth()->user()->id)
    //                 ->leftJoin('statustables as s', 's.id', '=', 'notifikasi.id_statustabel')
    //                 ->leftJoin('tabels as t', 't.id', '=', 's.id_tabel')
    //                 ->leftJoin('dinas as d', 'd.id', '=', 't.id_dinas')
    //                 ->leftJoin('users as u', 'u.id_dinas', '=', 'd.id')
    //                 ->orderBy('notifikasi.created_at', 'desc')
    //                 ->get([
    //                     'notifikasi.*',
    //                     't.label as judul_tabel',
    //                     's.tahun as tahundata',
    //                     's.status as status',
    //                 ]);
    //         }

    //         $years_all = Statustables::whereIn('id_tabel', $myTabels)->distinct()->orderBy('tahun')->pluck('tahun');
    //         #status 1
    //         $newTabels = Statustables::whereIn('id_tabel', $myTabels)
    //             ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
    //             ->where('status', 1)->count();
    //         // dd($newTabels);


    //         #status 2
    //         $entriTabels = Statustables::whereIn('id_tabel', $myTabels)
    //             ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
    //             ->where('status', 2)->count();

    //         #status 3
    //         $verifyTabels = Statustables::whereIn('id_tabel', $myTabels)
    //             ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
    //             ->where('status', 3)->count();

    //         #status 4
    //         $repairTabels = Statustables::whereIn('id_tabel', $myTabels)
    //             ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
    //             ->where('status', 4)->count();

    //         #status 5
    //         $finalTabels = Statustables::whereIn('id_tabel', $myTabels)
    //             ->whereIn('tahun', ($years == "all") ? $years_all : [$years])
    //             ->where('status', 5)->count();

    //         #total tabel
    //         $totalTabels = $newTabels + $entriTabels + $verifyTabels + $repairTabels + $finalTabels;

    //         if ($request->input('chart')) {
    //             return response()->json([
    //                 'newTabels' => $newTabels,
    //                 'entriTabels' => $entriTabels,
    //                 'verifyTabels' => $verifyTabels,
    //                 'repairTabels' => $repairTabels,
    //                 'finalTabels' => $finalTabels,
    //                 'totalTabels' => $totalTabels,
    //             ]);
    //         }
    //         return view('update-dashboard', compact(
    //             'newTabels',
    //             'entriTabels',
    //             'verifyTabels',
    //             'repairTabels',
    //             'finalTabels',
    //             'totalTabels',
    //             'notifikasiList'
    //         ))->render();
    //     }

    //     public function getMetaVariabel(Request $request)
    //     {
    //         $id_tabel = $request->id_tabel;
    //         // dd($id_tabel);
    //         $metavars = MetadataVariabel::join('metadata_variabel_status as mts', 'mts.id_tabel', '=', 'metadata_variabel.id_tabel')
    //             ->where('metadata_variabel.id_tabel', $id_tabel)
    //             ->where('mts.status', 5)
    //             ->get([
    //                 'metadata_variabel.*'
    //             ]);
    //         $satuan = Tabel::where('id', $id_tabel)->pluck('unit');
    //         if (sizeof($metavars) == 0) {
    //             # code...
    //             return response()->json([
    //                 'messages' => 'not',
    //             ]);
    //         }
    //         return view('show-metadata-variabel', compact(
    //             'metavars',
    //             'satuan',
    //         ));
    //     }

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
            $id_wilayah = MasterWilayah::getMyWilayahId();
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

    //     public function show(string $id, string $tahun)
    //     {
    //         $decryptedId = Crypt::decrypt($id);
    //         // $tabel = Tabel::where('id', $decryptedId)->first();
    //         // $statusTabel = Statustables::join('tabels AS t','t.id','statustables.id_tabel')
    //         // ->select('statustables')
    //         $statusTabel = Statustables::join('tabels as t', 'statustables.id_tabel', 't.id')
    //             ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
    //             ->select('t.id as id_tabel', 't.label as judul_tabel', 'statustables.tahun', 'sdesc.label as status', 'statustables.id as id_statustables')
    //             ->where('statustables.id', $decryptedId)->first();


    //         $id_tabel = $statusTabel->id_tabel;
    //         $tahun = $statusTabel->tahun;

    //         // $pattern = $id_tabel . '-%-' . $tahun . '-%';

    //         $datacontents = Datacontent::where('id_tabel', $id_tabel)->where('tahun', $tahun)->get();
    //         // return response()->json(['data' => $datacontents, 'pattern' => $pattern, 'id_status' => $decryptedId]);
    //         $id_rows = [];
    //         $wilayah_fullcodes = [];
    //         $id_columns = [];
    //         $tahuns = [];
    //         $turTahunKeys = [];

    //         foreach ($datacontents as $datacontent) {
    //             $split = explode("-", $datacontent->label);

    //             array_push($id_rows, $datacontent->id_row);
    //             array_push($id_columns, $datacontent->id_column);
    //             array_push($tahuns, $datacontent->tahun);
    //             array_push($turTahunKeys, $datacontent->id_turtahun);

    //             array_push($wilayah_fullcodes, $datacontent->wilayah_fullcode);
    //         }
    //         $tabels = Tabel::where('id', $id_tabel)->first();

    //         $rows = Row::whereIn('id', $id_rows)->get();
    //         $rowLabel = RowLabel::where('id', $rows[0]->id_rowlabels)->get();
    //         try {
    //             //code...
    //             if ($rows[0]->id == 0) {
    //                 // $wilayah_master = MasterWilayah::where('wilayah_fullcode','like',$wilayah_fullcodes[0]);
    //                 $wilayah_parent_code = '';
    //                 $jenis = "DAFTAR ";
    //                 $temp = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_fullcodes)
    //                     ->orderByRaw("CASE WHEN desa = '000' THEN 1 ELSE 0 END")
    //                     ->orderBy('desa')
    //                     ->get();
    //                 $rows = $temp;

    //                 $desa = substr($wilayah_fullcodes[0], 7, 3);
    //                 $kec = substr($wilayah_fullcodes[0], 4, 3);
    //                 $kab = substr($wilayah_fullcodes[0], 2, 2);
    //                 if ($desa != '000') {
    //                     $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 7) . '000';
    //                     $jenis = $jenis . "DESA DI ";
    //                 } else if ($kec != '000') {
    //                     $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 4) . '000' . '000';
    //                     $jenis = $jenis . "KECAMATAN DI ";
    //                 } else if ($kab != '00') {
    //                     $wilayah_parent_code = substr($wilayah_fullcodes[0], 0, 2) . '00' . '000' . '000';
    //                     $jenis = $jenis . "KABUPATEN DI ";
    //                 }
    //                 $rowLabel = $jenis . MasterWilayah::where('wilayah_fullcode', $wilayah_parent_code)->pluck('label')[0];
    //                 $rowLabel = strtolower($rowLabel);
    //                 $rowLabel = ucwords($rowLabel);
    //             } else {

    //                 $rowLabel = RowLabel::where('id', $rows[0]->id_rowlabels)->pluck('label')[0];
    //             }
    //         } catch (\Exception $e) {
    //             return response()->json(array('error' => $e->getMessage(), 'rows' => $rows));
    //         }

    //         $columns = Column::whereIn('id', $id_columns)->get();
    //         $tahuns = array_unique($tahuns);
    //         sort($tahuns);
    //         // $turtahuns = array_unique($turtahuns);
    //         // sort($turtahuns);
    //         $turtahuns = Turtahun::whereIn('id', $turTahunKeys)->get();


    //         // return response()->json(array('rows' => $tahuns));
    //         return view('view-tabel', [
    //             'datacontents' => $datacontents,
    //             'tabels' => $tabels,
    //             'encryptedId' => $id,
    //             'tahuns' => $tahuns,
    //             'tahun' => $tahun,
    //             'rows' => $rows,
    //             'row_label' => $rowLabel,
    //             'columns' => $columns,
    //             'turtahuns' => $turtahuns,
    //             'tabel' => $statusTabel
    //         ]);
    //     }
}
