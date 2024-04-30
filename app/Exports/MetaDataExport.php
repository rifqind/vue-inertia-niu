<?php

namespace App\Exports;

use App\Models\MetadataVariabel;
use App\Models\Tabel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MetaDataExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $tabel = Tabel::where('id', $this->id)->value('unit');
        $data = MetadataVariabel::where('id_tabel', $this->id)->get([
            'r101',
            'r102',
            'r103',
            'r104',
            'r105',
            'r106',
            'r107',
            'r108',
            'r109',
            'r110',
            'r111',
            'r112',
        ]);
        foreach ($data as $key => $value) {
            # code...
            $value->satuan = $tabel;
            if ($value->r112 == 1) {
                # code...
                $value->r112 = 'Ya';
            } else $value->r112 = 'Tidak';
        }
        return $data;
    }

    /**
     * @return array
     */

    public function headings(): array
    {
        return [
            'Nama Variabel',
            'Konsep',
            'Definisi',
            'Referensi Pemilihan',
            'Referensi Waktu',
            'Alias',
            'Ukuran',
            'Tipe Data',
            'Klasifikasi Isian',
            'Aturan Validasi',
            'Kalimat Pertanyaan',
            'Apakah Variabel Dapat Diakses?',
            'Unit',
        ];
    }
}
