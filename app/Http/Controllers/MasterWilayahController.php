<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterWilayah;

class MasterWilayahController extends Controller
{
    //
    public function fetchMasterKecamatan($kab)
    {
        $daftar_kecamatan = MasterWilayah::where('kab', 'like', $kab)->where('kec', 'not like', '000')->where('desa', 'like', '000')->get();
        return response()->json([
            'data' => $daftar_kecamatan,
            'status_code' => 200
        ]);
    }
    public function fetchMasterDesa($kab, $kec)
    {
        $daftar_desa = MasterWilayah::where('kab', 'like', $kab)->where('kec', 'like', $kec)->where('kec', 'not like', '000')->where('desa', 'not like', '000')->get();
        return response()->json([
            'data' => $daftar_desa,
            'status_code' => 200
        ]);
    }
}
