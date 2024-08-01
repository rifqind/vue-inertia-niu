<?php

namespace App\Exports;

use App\Models\MasterWilayah;
use App\Models\Statustables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TabelListExport implements FromCollection, WithHeadings
{
    protected $label;
    protected $produsen;
    protected $tahun;
    protected $status;
    protected $updatedBy;

    public function __construct(
        $label = null,
        $produsen = null,
        $tahun = null,
        $status = null,
        $updatedBy = null
    ) {
        $this->label = $label;
        $this->produsen = $produsen;
        $this->tahun = $tahun;
        $this->status = $status;
        $this->updatedBy = $updatedBy;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Statustables::query();
        // dd($this->produsen);
        $this_dinas = auth()->user()->id_dinas;
        $this_role = auth()->user()->role;
        if ($this_role == 'produsen') $query->where('dinas.id', $this_dinas);
        else $query->whereIn('dinas.wilayah_fullcode', MasterWilayah::getDinasWilayah());

        $query->join('tabels', 'statustables.id_tabel', '=', 'tabels.id')
            ->join('status_desc as sdesc', 'sdesc.id', '=', 'statustables.status')
            ->join('dinas', 'tabels.id_dinas', '=', 'dinas.id')
            ->join('subjects', 'subjects.id', '=', 'tabels.id_subjek')
            ->join('users', 'statustables.edited_by', '=', 'users.id')
            ->where('statustables.status', '<', 6)
            ->orderBy('statustables.status', 'desc')
            ->select(
                [
                    'tabels.nomor',
                    'tabels.label',
                    'dinas.nama',
                    'statustables.tahun',
                    'statustables.updated_at',
                    'users.username',
                    'sdesc.label as status',
                    'subjects.label as subjek'
                ]
            );
        if ($this->label != 'null')  $query->where(DB::raw("CONCAT(tabels.nomor, ' - ', tabels.label)"), 'like', '%' . $this->label . '%');
        // if ($this->produsen != 'null') $query->where('dinas.nama', 'like', '%' . $this->produsen . '%');
        if ($this->produsen != 'null') $query->where('dinas.nama', 'like', '%' . $this->produsen . '%');
        if ($this->tahun != 'null') $query->where('statustables.tahun', 'like', '%' . $this->tahun . '%');
        if ($this->status != 'null') $query->where('sdesc.label', 'like', '%' . $this->status . '%');
        if ($this->updatedBy != 'null') {
            $query->where(DB::raw("CONCAT(users.username, ' - ', tabels.updated_at)"), 'like', '%' . $this->updatedBy . '%');
        }
        $tabel = $query->get();
        foreach ($tabel as $key => $value) {
            # code...
            $value->label = $value->nomor . ' - ' . $value->label;
        }
        //want to get only tabel label (which modified above) dinas.nama, statustables.tahun, statustables.updated_at (which also modified above)
        $data = $tabel->map(function ($item) {
            return [
                'label' => $item->label,
                'nama' => $item->nama,
                'tahun' => $item->tahun,
                'subjek' => $item->subjek,
                'status' => $item->status,
                'edited_by' => $item->username,
                'updated_at' => $item->updated_at
            ];
        });
        return $data;
    }

    public function headings(): array
    {
        return [
            'Judul Tabel', 'Produsen Data', 'Tahun', 'Subjek', 'Status Data', 'User Terakhir', 'Terakhir di-Edit'
        ];
    }
}
