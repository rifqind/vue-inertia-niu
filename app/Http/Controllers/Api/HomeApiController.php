<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiList;
use App\Models\Column;
use App\Models\Datacontent;
use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\Row;
use App\Models\Statustables;
use App\Models\Subject;
use App\Models\Turtahun;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HomeApiController extends Controller
{
    private function getWilayah($wilayah)
    {
        if ($wilayah == "7100000000") {
            # code...
            $lists = MasterWilayah::pluck('wilayah_fullcode');
        } else {
            if (auth()->user()->role == 'admin') {
                # code...
                if ($wilayah == "7101000000") {
                    # code...
                    $lists = MasterWilayah::whereIn('kab', ['01', '10'])->pluck('wilayah_fullcode');
                } else if ($wilayah == "7105000000") {
                    $lists = MasterWilayah::whereIn('kab', ['05', '09'])->pluck('wilayah_fullcode');
                } else {
                    $lists = MasterWilayah::where('kab', auth()->user()->dinas->wilayah->kab)->pluck('wilayah_fullcode');
                }
            } else {
                $lists = MasterWilayah::where('kab', auth()->user()->dinas->wilayah->kab)->pluck('wilayah_fullcode');
            }
        }
        return $lists;
    }

    private function getMasterWilayah($wilayah)
    {
        $kab = substr($wilayah, 2, 4);
        if ($wilayah == "7100000000") {
            # code...
            $kabs = MasterWilayah::where('kec', 'like', '000')
                ->get(['label', 'wilayah_fullcode as value']);
            $kecs = MasterWilayah::where('kab', 'not like', '00')
                ->where('kec', 'not like', '000')
                ->where('desa', 'like', '000')
                ->get(['label', 'wilayah_fullcode as value']);
            $desa = MasterWilayah::where('kab', 'not like', '00')
                ->where('kec', 'not like', '000')
                ->where('desa', 'not like', '000')
                ->get(['label', 'wilayah_fullcode as value']);
        } else {
            $kabs = MasterWilayah::where('kab', auth()->user()->dinas->wilayah->kab)
                ->where('kec', 'like', '000')
                ->get(['label', 'wilayah_fullcode as value']);
            $kecs = MasterWilayah::where('kab', auth()->user()->dinas->wilayah->kab)
                ->where('kec', 'not like', '000')
                ->where('desa', 'like', '000')
                ->get(['label', 'wilayah_fullcode as value']);
            $desa = MasterWilayah::where('kab', auth()->user()->dinas->wilayah->kab)
                ->where('kec', 'not like', '000')
                ->where('desa', 'not like', '000')
                ->get(['label', 'wilayah_fullcode as value']);
        }
        $wilayah = [
            'kabs' => $kabs,
            'kecs' => $kecs,
            'desa' => $desa,
        ];
        return $wilayah;
    }

    public function index(Request $request, String $key)
    {
        $api = ApiList::where('key', $key)->first();
        if (!$api) return response()->json(['message' => 'API not found'], 404);
        if ($request->paginated) $paginated = $request->paginated;
        else $paginated = 10;
        if ($request->currentPage) $currentPage = $request->currentPage;
        else $currentPage = 1;
        $query = Statustables::query();
        $wilayah = $this->getWilayah($api->wilayah_fullcode);
        $query->whereIn('dinas.wilayah_fullcode', $wilayah);
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

    public function view(Request $request, $key)
    {
        $api = ApiList::where('key', $key)->first();
        if (!$api) return response()->json(['message' => 'API not found'], 404);
        $id_tabel = $request->id;
        $tahun = $request->tahun;

        $datacontents = Datacontent::where('id_tabel', $id_tabel)
            ->where('tahun', $tahun)
            ->get([
                'value',
                'id_row',
                'id_column',
                'id_turtahun',
                'tahun',
                'wilayah_fullcode'
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

    public function list(Request $request, $key)
    {
        $api = ApiList::where('key', $key)->first();
        if (!$api) return response()->json(['message' => 'API not found'], 404);
        $wilayah = $this->getWilayah($api->wilayah_fullcode);

        if ($request->list == 'dinas') {
            $data = Dinas::join('master_wilayah as mw', 'mw.wilayah_fullcode', '=', 'dinas.wilayah_fullcode')
                ->whereIn('dinas.wilayah_fullcode', $wilayah)
                ->select(['dinas.*', 'mw.label as wilayah_label'])
                ->get();
            return response()->json(['dinas' => $data]);
        }
        if ($request->list == 'subjek') {
            $data = Subject::get();
            return response()->json(['subjek' => $data]);
        }
        if ($request->list == 'wilayah') {
            $data =  $this->getMasterWilayah($api->wilayah_fullcode);
            return response()->json(['wilayah' => $data]);
        }
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'wilayah_fullcode' => ['required', 'string', 'max:10', 'unique:' . ApiList::class]
            ]);
            $data['key'] = $this->generateUniqueKey();
            ApiList::create($data);

            return redirect()->route('home-api.create');
        }
        if (auth()->user()->username == 'niu') {
            $data = ApiList::get();
        } else {
            $wilayah_dinas = Dinas::where('id', auth()->user()->id_dinas)->value('wilayah_fullcode');
            // dd($wilayah_dinas);
            $data = ApiList::where('wilayah_fullcode', $wilayah_dinas)->get();
        }
        $number = 1;
        foreach ($data as $key => $value) {
            # code...
            $value->number = $number;
            $number++;
        }
        $wilayah = MasterWilayah::getMyWilayah();
        return Inertia::render('Master/Api', [
            'api' => $data,
            'kabs' => $wilayah['kabs']
        ]);
    }
    private function generateUniqueKey()
    {
        do {
            # code...
            $key = Str::random(8);
        } while (ApiList::where('key', $key)->exists());
        return $key;
    }
}
