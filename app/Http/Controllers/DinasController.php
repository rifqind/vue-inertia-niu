<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use App\Models\MasterWilayah;
use Illuminate\Http\Request;
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
        $dinas = Dinas::orderBy('wilayah_fullcode')->orderBy('nama')
        ->leftJoin('master_wilayah as mw', 'mw.wilayah_fullcode', '=', 'dinas.wilayah_fullcode')    
        ->whereIn(
                'dinas.wilayah_fullcode',
                MasterWilayah::getDinasWilayah()
            )->get(['dinas.*', 'mw.label as wilayah_label']);
        foreach ($dinas as $din) {
            $din->number = $number;
            $number++;
        }

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
        $wilayah = MasterWilayah::getMyWilayah();
        return Inertia::render('Dinas/Create', [
            'kabs' => $wilayah["kabs"],
        ]);
    }

    public function fetch(string $id)
    {
        $dinas = Dinas::where('id', $id)->with('wilayah')->first();
        if ($dinas->wilayah->kab != '00') {
            # code...
            if ($dinas->wilayah->kec != '000') {
                # code...
                if ($dinas->wilayah->desa != '000') {
                    # code...
                    $dinas->tingkat = '3';
                } else {
                    $dinas->tingkat = '2';
                }
            } else {
                $dinas->tingkat = '1';
            }
        } else {
            $dinas->tingkat = '0';
        }
        $wilayah = MasterWilayah::getMyWilayah();
        $kabs = $wilayah['kabs'];
        return response()->json([
            'data' => $dinas,
            'kabs' => $kabs,
        ]);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    public function store(Request $request)
    {
        //
        $levels = $request->tingkat;
        switch ($levels) {
            case 0:
                # code...
                $wilayah_fullcode = (auth()->user()->dinas->wilayah_fullcode = "7100000000") ? "7100000000" : "";
                break;
            case 1:
                $wilayah_fullcode = $request->kab;
                break;
            case 2:
                $wilayah_fullcode = $request->kec;
                break;
            case 3:
                $wilayah_fullcode = $request->desa;
                break;
        }
        $request->merge(['wilayah_fullcode' => $wilayah_fullcode]);
        $request->validate([
            'nama' => ['required', 'string', 'unique:' . Dinas::class],
            'wilayah_fullcode' => ['required', 'string', 'max:10', 'min:10']
        ]);
        $dinas = Dinas::create([
            'nama' => $request->nama,
            'wilayah_fullcode' => $wilayah_fullcode,
        ]);
        return redirect()->route('dinas.index')->with('message', 'Berhasil menambahkan produsen data baru');
    }

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
    public function update(Request $request)
    {
        //
        // dd($request->nama);
        $id = $request->id;
        $levels = $request->tingkat;
        switch ($levels) {
            case 0:
                # code...
                $wilayah_fullcode = (auth()->user()->dinas->wilayah_fullcode = "7100000000") ? "7100000000" : "";
                break;
            case 1:
                $wilayah_fullcode = $request->kab;
                break;
            case 2:
                $wilayah_fullcode = $request->kec;
                break;
            case 3:
                $wilayah_fullcode = $request->desa;
                break;
        }
        $request->merge(['wilayah_fullcode' => $wilayah_fullcode]);
        $request->validate([
            'nama' => ['required', 'string', Rule::unique('dinas')->ignore($id)],
            'wilayah_fullcode' => ['required', 'string', 'max:10', 'min:10']
        ]);
        Dinas::where('id', $id)->update([
            'nama' => $request->nama,
            'wilayah_fullcode' => $wilayah_fullcode,
        ]);
        // return response()->json('Berhasil');
        return redirect()->route('dinas.index')->with('message', 'Berhasil mengedit data');
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    public function delete(Request $request)
    {
        //
        $id = $request->id;
        Dinas::destroy($id);
        return redirect()->route('dinas.index')->with('message', 'Berhasil menghapus data');
    }
}
