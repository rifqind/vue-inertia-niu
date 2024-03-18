<?php

namespace Database\Seeders;

use App\Models\Tabel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tabels = [
            [
                'nomor' => '1.1b',
                'label' => 'Jumlah Samurai yang tersedia untuk Perang Terakhir Melawan Kaidou',
                'unit' => 'Orang',
                'id_dinas' => 1,
                'id_subjek' => 2,
                'created_at' => '2024-01-11 16:54:28',
                'updated_at' => '2024-02-12 21:24:16'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Pasukan yang dapat dibawa ke Laugh Tale',
                'unit' => 'Orang',
                'id_dinas' => 11,
                'id_subjek' => 2,
                'created_at' => '2024-01-11 22:34:29',
                'updated_at' => '2024-01-11 22:34:29'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Pabrik Slime yang beroperasi dengan baik di Dressrosa Bagian Utara',
                'unit' => 'Pabrik',
                'id_dinas' => 3,
                'id_subjek' => 2,
                'created_at' => '2024-01-14 17:00:44',
                'updated_at' => '2024-01-14 17:00:44'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Manusia Ikan yang bebas setelah Pembebasan Berkala oleh JoyBoy',
                'unit' => 'Manusia Ikan',
                'id_dinas' => 4,
                'id_subjek' => 4,
                'created_at' => '2024-01-14 17:23:35',
                'updated_at' => '2024-01-14 17:23:35'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Agen Cipher Pol yang tersedia untuk menahan Straw Hat Crew',
                'unit' => 'Manusia',
                'id_dinas' => 5,
                'id_subjek' => 4,
                'created_at' => '2024-01-14 17:28:34',
                'updated_at' => '2024-01-14 17:28:34'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Awak Kapal yang bisa dibawa ke Marine Fort',
                'unit' => 'Orang',
                'id_dinas' => 7,
                'id_subjek' => 4,
                'created_at' => '2024-01-14 17:36:43',
                'updated_at' => '2024-01-14 17:36:43'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Eksekutif Keluarga Donquixote? yang siap untuk melawan Mugiwara Fleet',
                'unit' => 'Manusia',
                'id_dinas' => 3,
                'id_subjek' => 4,
                'created_at' => '2024-01-15 23:02:00',
                'updated_at' => '2024-01-15 23:02:00'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Persentase kemenangan Doflamingo jika Luffy tidak punya plot armor',
                'unit' => 'Persen',
                'id_dinas' => 3,
                'id_subjek' => 4,
                'created_at' => '2024-01-17 22:48:20',
                'updated_at' => '2024-01-17 22:48:20'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Persentase Keberhasilan Pemeriksaaan Data yang ada per Desa di Dressrosa Bagian Utara',
                'unit' => 'Persen',
                'id_dinas' => 3,
                'id_subjek' => 4,
                'created_at' => '2024-01-18 18:05:49',
                'updated_at' => '2024-01-18 18:05:49'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah kru yang bisa dibawa ke Kuja Island di Skull Island per Kabupaten',
                'unit' => 'Orang',
                'id_dinas' => 6,
                'id_subjek' => 7,
                'created_at' => '2024-01-22 17:26:19',
                'updated_at' => '2024-01-22 17:26:19'
            ],
            [
                'nomor' => 'TBD',
                'label' => 'Jumlah Reservasi Hutan Lindung per Jenis Kelamin dan Nilai Pendapatan PNS di Kabupaten Minahasa Utara',
                'unit' => 'Hutan',
                'id_dinas' => 4,
                'id_subjek' => 7,
                'created_at' => '2024-01-24 19:37:37',
                'updated_at' => '2024-01-24 19:37:37'
            ],
            [
                'nomor' => '1.1a',
                'label' => 'Jumlah Lahan per Kabupaten/Kota',
                'unit' => 'Meter/Persegi',
                'id_dinas' => 17,
                'id_subjek' => 3,
                'created_at' => '2024-02-06 22:03:00',
                'updated_at' => '2024-02-06 22:03:00'
            ],
            [
                'nomor' => '1.2.1a',
                'label' => 'Jumlah Warga per Jenis Kelamin di Kecamatan Wanea',
                'unit' => 'Orang',
                'id_dinas' => 20,
                'id_subjek' => 1,
                'created_at' => '2024-02-15 23:48:17',
                'updated_at' => '2024-02-15 23:48:17'
            ],
            [
                'nomor' => '1.2.1a',
                'label' => 'Jumlah Warga Per Jenis Kelamin di Kelurahan Wanea',
                'unit' => 'Orang',
                'id_dinas' => 19,
                'id_subjek' => 1,
                'created_at' => '2024-02-15 23:50:14',
                'updated_at' => '2024-02-15 23:50:14'
            ],
            [
                'nomor' => '1.2.1a',
                'label' => 'Jumlah Warga Per Jenis Kelamin di Kecamatan Tompaso',
                'unit' => 'Orang',
                'id_dinas' => 25,
                'id_subjek' => 1,
                'created_at' => '2024-02-19 16:39:23',
                'updated_at' => '2024-02-19 16:39:23'
            ],
            [
                'nomor' => '1.2.1a',
                'label' => 'Jumlah Warga Per Jenis Kelamin di Desa Kamanga',
                'unit' => 'Orang',
                'id_dinas' => 26,
                'id_subjek' => 1,
                'created_at' => '2024-02-19 16:40:23',
                'updated_at' => '2024-02-19 16:40:23'
            ],
        ];
        foreach ($tabels as $tabel) {
            Tabel::create($tabel);
        }
    }
}
