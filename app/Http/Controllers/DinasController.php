<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DinasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $number = 1;
        $wilayah = MasterWilayah::getMyWilayah();
        $id_wilayah = MasterWilayah::getMyWilayahId();
        $kabs = $wilayah["kabs"];
        // dd($id_wilayah["kabs"]);
        $dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')->whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->with('wilayah')->get();
        foreach ($dinas as $din) {
            $din->number = $number;
            $number++;
        }

        // return view('dinas.index', [
        //     'dinas' => $dinas,
        //     'kabs' => $kabs
        // ]);
        return Inertia::render('Dinas/Index', [
            'dinas' => $dinas,
            'kabs' => $kabs,
        ]);
    }
    

    // /**
    //  * Show the form for creating a new resource.
    //  */
    public function create()
    {
        //
        // $regions = Region::all();
        $wilayah = MasterWilayah::getMyWilayah();
        // $id_regions = Region::getRegionId();
        // return view('dinas.create', [
        //     'kabs' => $wilayah["kabs"]
        // ]);
        return Inertia::render('Dinas/Create', [
            'kabs' => $wilayah["kabs"],
        ]);
    }

    // public function search(Request $request)
    // {
    //     $searchQuery = $request->query('search');

    //     $dinas = Dinas::when($searchQuery, function ($query) use ($searchQuery) {
    //         return $query
    //             ->where('dinas.nama', 'like', '%' . $searchQuery . '%')
    //             ->orWhere('master_wilayah.label', 'like', '%' . $searchQuery . '%');
    //     })
    //         ->leftJoin('master_wilayah', 'dinas.wilayah_fullcode', '=', 'master_wilayah.wilayah_fullcode')
    //         ->orderBy('dinas.nama')
    //         ->get(['dinas.*', 'master_wilayah.label as region_nama']);

    //     $dinas->each(function ($din, $key) {
    //         $din->number = $key + 1;
    //     });
    //     // return view('dinas.index', compact('dinas'));
    //     return response()->json(['dinas' => $dinas]);
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     //
    //     $levels = $request->tingkat;
    //     switch ($levels) {
    //         case 0:
    //             # code...
    //             $wilayah_fullcode = (auth()->user()->dinas->wilayah_fullcode = "7100000000") ? "7100000000" : "";
    //             break;
    //         case 1:
    //             $wilayah_fullcode = $request->kab;
    //             break;
    //         case 2:
    //             $wilayah_fullcode = $request->kec;
    //             break;
    //         case 3:
    //             $wilayah_fullcode = $request->desa;
    //             break;
    //     }
    //     $request->merge(['wilayah_fullcode' => $wilayah_fullcode]);
    //     $request->validate([
    //         'nama' => ['required', 'string', 'unique:'.Dinas::class],
    //         'wilayah_fullcode' => ['required', 'string', 'max:10', 'min:10']
    //     ]);
    //     $dinas = Dinas::create([
    //         'nama' => $request->nama,
    //         'wilayah_fullcode' => $wilayah_fullcode,
    //     ]);
    //     return response()->json('Berhasil');
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request)
    // {
    //     //
    //     $id = $request->id;
    //     $decryptedId = Crypt::decrypt($id);
    //     $levels = $request->tingkat;
    //     switch ($levels) {
    //         case 0:
    //             # code...
    //             $wilayah_fullcode = (auth()->user()->dinas->wilayah_fullcode = "7100000000") ? "7100000000" : "";
    //             break;
    //         case 1:
    //             $wilayah_fullcode = $request->kab;
    //             break;
    //         case 2:
    //             $wilayah_fullcode = $request->kec;
    //             break;
    //         case 3:
    //             $wilayah_fullcode = $request->desa;
    //             break;
    //     }
    //     $request->merge(['wilayah_fullcode' => $wilayah_fullcode]);
    //     $request->validate([
    //         'nama' => ['required', 'string', Rule::unique('dinas')->ignore($decryptedId)],
    //         'wilayah_fullcode' => ['required', 'string', 'max:10', 'min:10']
    //     ]);
    //     Dinas::where('id', $decryptedId)->update([
    //         'nama' => $request->nama,
    //         'wilayah_fullcode' => $wilayah_fullcode,
    //     ]);
    //     return response()->json('Berhasil');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function delete(Request $request)
    // {
    //     //
    //     $id = $request->id;
    //     $decryptedId = Crypt::decrypt($id);
    //     Dinas::destroy($decryptedId);
    //     return response()->json('Berhasil Hapus');
    // }
}
