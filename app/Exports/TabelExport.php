<?php

namespace App\Exports;

use App\Models\Tabel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TabelExport implements FromCollection, WithHeadings
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $tabel = Tabel::where('tabels.id', $this->id)
            ->leftJoin('dinas as d', 'tabels.id_dinas', '=', 'd.id')
            ->leftJoin('subjects as s', 'tabels.id_subjek', '=', 's.id')
            ->get([
                'tabels.nomor', 'tabels.label', 'tabels.unit',
                'd.nama', 's.label as subject_label'
            ]);
        return $tabel;
    }

    public function headings(): array
    {
        return [
            'Nomor Tabel', 'Judul Tabel', 'Unit', 'Produsen Data', 'Subjek Data'
        ];
    }
}
