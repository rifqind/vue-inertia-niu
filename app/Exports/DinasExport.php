<?php

namespace App\Exports;

use App\Models\Dinas;
use App\Models\MasterWilayah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DinasExport implements FromCollection, WithHeadings
{
    protected $nama;
    protected $wilayah_label;

    public function __construct(
        $nama = null,
        $wilayah_label = null,
    ) {
        $this->nama = $nama;
        $this->wilayah_label = $wilayah_label;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        //
        $query = Dinas::query();
        $query->join('master_wilayah as mw', 'mw.wilayah_fullcode', '=', 'dinas.wilayah_fullcode')
            ->whereIn(
                'dinas.wilayah_fullcode',
                MasterWilayah::getDinasWilayah()
            )->orderBy('dinas.wilayah_fullcode', 'ASC')
            ->select(['dinas.*', 'mw.label as wilayah_label']);
        if ($this->nama != 'null') {
            $query->where('nama', 'like', '%' .  $this->nama . '%');
        }
        if ($this->wilayah_label != 'null') {
            $query->where('mw.label', 'like', '%' . $this->wilayah_label . '%');
        }

        $dinas = $query->get();
        // dd($dinas[0]);
        $data = $dinas->map(function ($item) {
            return [
                'nama' => $item->nama,
                'wilayah_label' => $item->wilayah_label
            ];
        });
        // dd($data);
        return $data;
    }

    public function headings(): array
    {
        return [
            'Nama Produsen Data', 'Wilayah Kerja'
        ];
    }
}
