<?php

namespace App\Http\Controllers;

use App\Exports\MetaDataExport;
use App\Models\MasterWilayah;
use App\Models\MetadataVariabel;
use App\Models\MetadataVariabelStatus;
use App\Models\Tabel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class MetadataVariabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $this_dinas = auth()->user()->id_dinas;
        $this_role = auth()->user()->role;

        if ($this_role == 'produsen') {
            # code...
            $tabels = Tabel::where('id_dinas', $this_dinas)
                ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
                ->leftJoin('metadata_variabel_status as mtv', 'mtv.id_tabel', '=', 'tabels.id')
                ->leftJoin('status_desc as sdc', 'sdc.id', '=', 'mtv.status')
                ->get(['tabels.*', 'dinas.nama as nama_dinas', 'sdc.label as status_desc', 'tabels.updated_at as status_updated']);
        } else {
            $tabels = Tabel::whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah())
                ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
                ->leftJoin('metadata_variabel_status as mtv', 'mtv.id_tabel', '=', 'tabels.id')
                ->leftJoin('status_desc as sdc', 'sdc.id', '=', 'mtv.status')
                ->get(['tabels.*', 'dinas.nama as nama_dinas', 'sdc.label as status_desc', 'tabels.updated_at as status_updated']);
        }
        foreach ($tabels as $key => $tabel) {
            # code...
            $tabel->number = $key + 1;
            $checkMetavar = MetadataVariabelStatus::where('id_tabel', $tabel->id)->first(['*', 'updated_at as status_updated']);
            // dd($checkMetavar);
            if (!$checkMetavar) {
                # code...
                $tabel->status_metavar = '0';
                $tabel->when_updated = "-";
                $tabel->who_updated = "-";
            } else {
                $tabel->status_metavar = $checkMetavar->status;
                $checkRealMetavar = MetadataVariabel::where('id_tabel', $tabel->id)->latest('updated_at')->first('updated_at as status_updated');
                $tabel->when_updated = $checkRealMetavar->status_updated;
                $tabel->who_updated = User::where('id', $checkMetavar->edited_by)->value('username');
            }
        }
        return Inertia::render('Metavar/Index', [
            'tabels' => $tabels,
        ]);
    }

    public function lists(string $id)
    {
        $this_role = auth()->user()->role;
        $master_metavar = MetadataVariabel::selectRaw('MIN(id) as id, r101')->groupBy('r101')->get();
        // dd($master_metavar);
        $this_metavar = MetadataVariabel::where('id_tabel', $id)->get();
        $this_status_metavar = MetadataVariabelStatus::where('id_tabel', $id)->value('status');
        $status_desc = MetadataVariabelStatus::where('id_tabel', $id)->join('status_desc as sdc', 'sdc.id', '=', 'metadata_variabel_status.status')->value('sdc.label');
        // dd($status_desc);
        $judul = Tabel::where('id', $id)->value('label');
        $satuan = Tabel::where('id', $id)->value('unit');
        // dd($this_metavar);
        foreach ($this_metavar as $key => $value) {
            # code...
            $value->number = $key + 1;
            $value->satuan = $satuan;
        }
        $id_tabel = $id;
        return Inertia::render('Metavar/List', [
            'metavars' => $this_metavar,
            'status_metavars' => $this_status_metavar,
            'status_desc' => $status_desc,
            'judul' => $judul,
            'satuan' => $satuan,
            'master_metavar' => $master_metavar,
            'id_tabel' => $id_tabel,
        ]);
    }

    public function store(Request $request)
    {
        //
        $id_tabel = $request->id_tabel;
        $validatedData = $request->validate([
            'r101' => 'required',
            'r102' => 'required',
            'r103' => 'required',
            'r104' => 'required',
            'r105' => 'required',
            'r106' => 'required',
            'r107' => 'required',
            'r108' => 'required',
            'r109' => 'required',
            'r110' => 'required',
            'r111' => 'required',
            'r112' => 'required',
        ]);
        if ($request->id) {
            $validatedData['id_tabel'] = $id_tabel;
            MetadataVariabel::where('id', $request->id)->update($validatedData);
            return redirect()->route('metavar.lists', ['id' => $id_tabel])->with('message', 'Berhasil mengedit data');
        }
        try {
            //code...
            DB::beginTransaction();
            $metavar = MetadataVariabel::create([
                'id_tabel' => $id_tabel,
                'r101' => $request->r101,
                'r102' => $request->r102,
                'r103' => $request->r103,
                'r104' => $request->r104,
                'r105' => $request->r105,
                'r106' => $request->r106,
                'r107' => $request->r107,
                'r108' => $request->r108,
                'r109' => $request->r109,
                'r110' => $request->r110,
                'r111' => $request->r111,
                'r112' => $request->r112,
            ]);

            $check_metavar_status = MetadataVariabelStatus::where('id_tabel', $id_tabel)->first();
            if (!$check_metavar_status) {
                # code...
                $metavar_status = MetadataVariabelStatus::where('id_tabel', $id_tabel)->create([
                    'id_tabel' => $id_tabel,
                    'status' => 2,
                    'edited_by' => auth()->user()->id,
                ]);
            } else {
                // dd($check_metavar_status);
                $check_metavar_status->update([
                    'status' => 2,
                    'edited_by' => auth()->user()->id,
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'message' => $th->getMessage(), // Include the exception message
            ], 500);
        }
        return redirect()->route('metavar.lists', ['id' => $id_tabel])->with('message', 'Berhasil menambah data baru');
    }

    public function export(string $id, $title)
    {
        return Excel::download(new MetaDataExport($id), $title . ".xlsx");
    }

    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        $decryptedId = decrypt($request->id);
        $this_metavar = MetadataVariabel::find($decryptedId);
        // dd($decryptedId);
        return response()->json($this_metavar);
    }

    public function fetchMaster(String $id)
    {
        $this_metavar =  ($id != '0') ? MetadataVariabel::find($id) : "not";
        if ($this_metavar !== "not") {
            unset($this_metavar->id_tabel);
        }
        return response()->json($this_metavar);
    }

    public function fetchData(String $id)
    {
        $this_metavar = MetadataVariabel::find($id);
        // dd($this_metavar);
        return response()->json($this_metavar);
    }

    public function metavarSend(string $id)
    {
        $this_metavar_status = MetadataVariabelStatus::where('id_tabel', $id)->update([
            'status' => 3,
            'edited_by' => auth()->user()->id,
        ]);

        return redirect()->route('metavar.lists', ['id' => $id])->with('message', 'Berhasil mengirim metadata untuk diperiksa');
    }

    public function adminHandleMetavar(Request $request)
    {
        $decisions = $request->decisions;

        $this_metavar_status = MetadataVariabelStatus::where('id_tabel', $request->id_tabel)->update([
            'status' => ($decisions == "reject") ? 4 : 5,
            'edited_by' => auth()->user()->id,
        ]);
        return redirect()->route('metavar.lists', ['id' => $request->id_tabel])->with('message', 'Berhasil');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $get_idTabel = MetadataVariabel::where('id', $request->id)->value('id_tabel');
        MetadataVariabel::destroy($request->id);
        $check = MetadataVariabel::where('id_tabel', $get_idTabel)->get();
        if ($check->isEmpty()) {
            $id_metavar_status = MetadataVariabelStatus::where('id_tabel', $get_idTabel)->value('id');
            MetadataVariabelStatus::destroy($id_metavar_status);
        }
        return redirect()->route('metavar.lists', ['id' => $get_idTabel])->with('message', 'Berhasil menghapus data tersebut');
    }
}
