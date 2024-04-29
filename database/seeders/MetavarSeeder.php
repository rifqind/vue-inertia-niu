<?php

namespace Database\Seeders;

use App\Models\MetadataVariabel;
use Illuminate\Database\Seeder;

class MetavarSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_tabel' => '9b975a0a-481c-4754-93ed-df36f72cd2d7',
                'r101' => 'Cukup Sekali',
                'r102' => 'Gaji 2 1142',
                'r103' => '-',
                'r104' => '-',
                'r105' => '2023',
                'r106' => '-',
                'r107' => '-',
                'r108' => '-',
                'r109' => '-',
                'r110' => '-',
                'r111' => '-',
                'r112' => 1,
                'created_at' => '2024-01-29 22:54:15',
                'updated_at' => '2024-01-29 22:54:15'
            ],
            [
                'id_tabel' => '9b975a0a-11ca-44ab-916f-bbbcf907a966',
                'r101' => 'Devil Fruit',
                'r102' => 'Buah',
                'r103' => 'Buah yang dapat memberi kekuatan laut',
                'r104' => 'PP No. 1 Marine',
                'r105' => '1999',
                'r106' => '-',
                'r107' => 'Besar',
                'r108' => 'Integer',
                'r109' => '-',
                'r110' => '-',
                'r111' => '-',
                'r112' => 1,
                'created_at' => '2024-01-30 22:49:22',
                'updated_at' => '2024-01-30 22:49:22'
            ],
            [
                'id_tabel' => '9b975a0a-500c-408b-ad15-233af68eaa22',
                'r101' => 'Devil Fruit User',
                'r102' => 'Manusia',
                'r103' => 'Orang kedua yang memakan Devil Fruit',
                'r104' => '-',
                'r105' => '2023',
                'r106' => '-',
                'r107' => 'Besar',
                'r108' => 'Integer',
                'r109' => '-',
                'r110' => '-',
                'r111' => '-',
                'r112' => 1,
                'created_at' => '2024-02-06 22:15:54',
                'updated_at' => '2024-02-06 22:16:31'
            ],
            [
                'id_tabel' => '9b975a0a-0918-492b-ac27-88ad07649e31',
                'r101' => 'Variabelku hanya empat',
                'r102' => 'Konsepku',
                'r103' => 'Definisiku itu dia',
                'r104' => 'Ref Pemilihanku',
                'r105' => '2023',
                'r106' => '-',
                'r107' => 'Besar',
                'r108' => 'Integer',
                'r109' => '-',
                'r110' => '-',
                'r111' => '-',
                'r112' => 1,
                'created_at' => '2024-02-19 21:53:45',
                'updated_at' => '2024-02-19 21:53:45'
            ],
            [
                'id_tabel' => '9b975a0a-7cd3-4aef-bd55-5a1ad4ba8ae7',
                'r101' => 'Kelurahan Wanea',
                'r102' => 'Daerah',
                'r103' => 'Wilayah administrasi di bawah Kota Manado',
                'r104' => 'UU No. 12 Mendagri',
                'r105' => '2023',
                'r106' => '-',
                'r107' => 'Besar',
                'r108' => 'Integer',
                'r109' => '-',
                'r110' => '-',
                'r111' => '-',
                'r112' => 1,
                'created_at' => '2024-02-22 03:44:22',
                'updated_at' => '2024-02-22 03:44:22'
            ],
            [
                'id_tabel' => '9b975a09-8fd2-4cff-911f-2fd3469bfa83',
                'r101' => 'Devil Fruit',
                'r102' => 'Buah',
                'r103' => 'Buah yang dapat memberi kekuatan laut',
                'r104' => 'PP No. 1 Marine',
                'r105' => '1999',
                'r106' => '-',
                'r107' => 'Besar',
                'r108' => 'Integer',
                'r109' => '-',
                'r110' => '-',
                'r111' => '-',
                'r112' => 1,
                'created_at' => '2024-02-29 02:07:27',
                'updated_at' => '2024-02-29 02:07:27'
            ]
        ];
        foreach ($data as $key => $value) {
            # code...
            MetadataVariabel::create($value);
        }
    }
}
