<?php

namespace App\Exports;

use App\Models\Dinas;
use App\Models\MasterWilayah;
use App\Models\Statustables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonitoringExport implements FromCollection, WithHeadings
{
    protected $label;
    protected $wilayah;
    protected $tahun;

    public function __construct($label = null, $wilayah = null, $tahun = null)
    {
        $this->label = $label;
        $this->wilayah = $wilayah;
        $this->tahun = $tahun;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $ourDinas = Dinas::whereIn('wilayah_fullcode', MasterWilayah::getDinasWilayah())->pluck('id');
        $query = Statustables::query();

        if ($this->label != 'null') {
            $query->where('d.nama', 'like', '%' . $this->label . '%');
        }
        if ($this->wilayah != 'null') {
            $query->where('statustables.tahun', $this->tahun);
        }
        if ($this->tahun != 'null') {
            $query->where('d.wilayah_fullcode', $this->wilayah);
        }

        $this_monitoring = $query->join('tabels as t', 't.id', '=', 'statustables.id_tabel')
            ->join('dinas as d', 'd.id', '=', 't.id_dinas')
            ->select(
                'd.nama as nama_dinas',
                DB::raw('count(case when statustables.status = 1 then 1 end) as jumlah_satu'),
                DB::raw('count(case when statustables.status = 2 then 1 end) as jumlah_dua'),
                DB::raw('count(case when statustables.status = 3 then 1 end) as jumlah_tiga'),
                DB::raw('count(case when statustables.status = 4 then 1 end) as jumlah_empat'),
                DB::raw('count(case when statustables.status = 5 then 1 end) as jumlah_lima'),
                DB::raw('count(case when statustables.status = 6 then 1 end) as jumlah_enam')
            )
            ->whereIn('t.id_dinas', $ourDinas)
            ->groupBy('d.nama');

        $data = $this_monitoring->get();
        foreach ($data as $key => $value) {
            if ($value->jumlah_satu == 0) $value->jumlah_satu = '-';
            if ($value->jumlah_dua == 0) $value->jumlah_dua = '-';
            if ($value->jumlah_tiga == 0) $value->jumlah_tiga = '-';
            if ($value->jumlah_empat == 0) $value->jumlah_empat = '-';
            if ($value->jumlah_lima == 0) $value->jumlah_lima = '-';
            if ($value->jumlah_enam == 0) $value->jumlah_enam = '-';
        }
        // dd($data[0]);
        return $data;
    }

    public function headings(): array
    {
        return [
            'Produsen Data', 'Status Tabel Baru',
            'Status Proses Entri', 'Status Diperiksa',
            'Status Perbaikan', 'Status Final', 'Tabel Dihapus'
        ];
    }
}
