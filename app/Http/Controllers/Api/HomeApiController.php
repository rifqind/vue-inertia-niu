<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Column;
use App\Models\Datacontent;
use App\Models\MasterWilayah;
use App\Models\Row;
use App\Models\Statustables;
use App\Models\Turtahun;
use Illuminate\Http\Request;

class HomeApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;
        $query = Statustables::query();
        $dataToCounted = $query
            ->where('status', 5)
            ->join('tabels', 'statustables.id_tabel', '=', 'tabels.id')
            ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
            ->join('master_wilayah', 'dinas.wilayah_fullcode', '=', 'master_wilayah.wilayah_fullcode')
            ->join('subjects', 'tabels.id_subjek', '=', 'subjects.id')
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
            ]);
        if ($request->ArrayFilter) {
            $filter = $request->ArrayFilter;
            if (!empty($filter['tahun'])) {
                $filter['tahun'] = array_values(array_filter($filter['tahun'], function ($value) {
                    return $value !== 'all';
                }));
                if (!empty($filter['tahun'])) $query->whereIn('statustables.tahun', $filter['tahun']);
            }
            if (!empty($filter['kode'])) $query->whereIn('master_wilayah.wilayah_fullcode', $filter['kode']);
            if (!empty($filter['dinas'])) {
                $filter['dinas'] = array_values(array_filter($filter['dinas'], function ($value) {
                    return $value !== 'all';
                }));
                if (!empty($filter['dinas'])) $query->whereIn('dinas.id', $filter['dinas']);
            }
            if (!empty($filter['subjek'])) $query->whereIn('subjects.id', $filter['subjek']);
            if (!empty($filter['label'])) {
                $query
                    ->where('master_wilayah.label', 'like', '%' . $filter['label'] . '%')
                    ->orWhere('statustables.tahun', 'like', '%' . $filter['label'] . '%')
                    ->orWhere('dinas.nama', 'like', '%' . $filter['label'] . '%')
                    ->orWhere('subjects.label', 'like', '%' . $filter['label'] . '%')
                    ->orWhere('tabels.label', 'like', '%' . $filter['label'] . '%')
                    ->orWhere('statustables.updated_at', 'like', '%' . $filter['label'] . '%');
            }
        }
        $countData = $dataToCounted->count();
        $tabels = $query->paginate($paginated, ['*'], 'page', $currentPage);

        return response()->json([
            'countTabels' => $countData,
            'tabels' => $tabels,
        ]);
    }

    public function view(Request $request)
    {
        $id_tabel = $request->id;
        $tahun = $request->tahun;

        $datacontents = Datacontent::where('id_tabel', $id_tabel)
            ->where('tahun', $tahun)
            ->get([
                'value', 'id_row', 'id_column', 'id_turtahun', 'tahun', 'wilayah_fullcode'
            ]);
        $columnList = Datacontent::where('id_tabel', $id_tabel)
            ->where('tahun', $tahun)
            ->pluck('id_column');
        $rowList = Datacontent::where('id_tabel', $id_tabel)
            ->where('tahun', $tahun)
            ->pluck('id_row');
        $turtahunList = Datacontent::where('id_tabel', $id_tabel)
            ->where('tahun', $tahun)
            ->pluck('id_turtahun');
        $wilayah_label = Datacontent::where('id_tabel', $id_tabel)
            ->where('tahun', $tahun)
            ->pluck('wilayah_fullcode');

        $columns = Column::whereIn('id', $columnList)->get(
            ['id', 'label']
        );
        $rows = Row::whereIn('id', $rowList)->get(
            ['id', 'label']
        );
        $turtahuns = Turtahun::whereIn('id', $turtahunList)->get(
            ['id', 'label']
        );
        $wilayah_label = MasterWilayah::whereIn('wilayah_fullcode', $wilayah_label)->get(
            ['wilayah_fullcode', 'label']
        );
        return response()->json([
            'data' => $datacontents,
            'columns' => $columns,
            'rows' => $rows,
            'turtahuns' => $turtahuns,
            'wilayah_label' => $wilayah_label,
        ]);
    }
}
