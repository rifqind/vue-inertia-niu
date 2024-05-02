<?php

namespace App\Http\Controllers;

use App\Models\MasterWilayah;

class MasterWilayahController extends Controller
{
    //
    public function fetchMasterKecamatan(string $kab)
    {
        $daftar_kecamatan = MasterWilayah::where('kab', 'like', $kab)->where('kec', 'not like', '000')->where('desa', 'like', '000')->get(['label', 'wilayah_fullcode as value']);
        return response()->json([
            'data' => $daftar_kecamatan,
            'status_code' => 200
        ]);
    }
    public function fetchMasterDesa($kab, $kec)
    {
        $daftar_desa = MasterWilayah::where('kab', 'like', $kab)->where('kec', 'like', $kec)->where('kec', 'not like', '000')->where('desa', 'not like', '000')->get(['label', 'wilayah_fullcode as value']);
        return response()->json([
            'data' => $daftar_desa,
            'status_code' => 200
        ]);
    }
}
