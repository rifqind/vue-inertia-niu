<?php

namespace Database\Seeders;

use App\Models\Statustables;
use App\Models\Tabel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatustableSeeder extends Seeder
{
    public function run(): void
    {
        $status_tables =
            [
                ['id_tabel' => '9b975a09-8fd2-4cff-911f-2fd3469bfa83', 'tahun' => '2023', 'status' => 2, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-0918-492b-ac27-88ad07649e31', 'tahun' => '2022', 'status' => 4, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-11ca-44ab-916f-bbbcf907a966', 'tahun' => '2022', 'status' => 4, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-21b6-41b5-a161-fbe22bd5a5e1', 'tahun' => '2022', 'status' => 1, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-34e9-4a14-9136-18ae84c5c822', 'tahun' => '2022', 'status' => 1, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-3ce8-4c69-95b3-ab79f212e8bf', 'tahun' => '2021', 'status' => 1, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-481c-4754-93ed-df36f72cd2d7', 'tahun' => '2020', 'status' => 6, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a09-8fd2-4cff-911f-2fd3469bfa83', 'tahun' => '2024', 'status' => 6, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-0918-492b-ac27-88ad07649e31', 'tahun' => '2023', 'status' => 1, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-11ca-44ab-916f-bbbcf907a966', 'tahun' => '2023', 'status' => 4, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-500c-408b-ad15-233af68eaa22', 'tahun' => '2022', 'status' => 2, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-5809-4eeb-bedc-18f13c321c48', 'tahun' => '2020', 'status' => 5, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-6017-40eb-988e-d032b52977c4', 'tahun' => '2020', 'status' => 1, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-67fb-4f85-8218-54b824ab0a15', 'tahun' => '2023', 'status' => 4, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-6ffe-4070-b9ee-9c354589d9bb', 'tahun' => '2023', 'status' => 1, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-74b4-4982-9c4b-175620e287d9', 'tahun' => '2024', 'status' => 5, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-7cd3-4aef-bd55-5a1ad4ba8ae7', 'tahun' => '2024', 'status' => 5, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-84ea-4cd4-8fd0-fe90dd7a4ed6', 'tahun' => '2024', 'status' => 5, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9b975a0a-9b64-4c08-b442-9cd9798c85a9', 'tahun' => '2024', 'status' => 5, 'created_at' => '2024-03-19 03:08:32', 'updated_at' => '2024-03-19 03:08:32', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9ba57c2c-d8d8-404c-a273-9ed256e39d2f', 'tahun' => '2024', 'status' => 1, 'created_at' => '2024-03-24 23:09:12', 'updated_at' => '2024-03-24 23:09:12', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9ba705b4-574a-4015-8bb7-74aad51a677e', 'tahun' => '2024', 'status' => 1, 'created_at' => '2024-03-25 17:29:35', 'updated_at' => '2024-03-25 17:29:35', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9ba7089f-b0af-4f47-90ce-5e38a2926658', 'tahun' => '2024', 'status' => 1, 'created_at' => '2024-03-25 17:37:45', 'updated_at' => '2024-03-25 17:37:45', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2'],
                ['id_tabel' => '9ba709ca-f252-4f52-8b0f-02563dbb7ffb', 'tahun' => '2024', 'status' => 1, 'created_at' => '2024-03-25 17:41:01', 'updated_at' => '2024-03-25 17:41:01', 'edited_by' => '9b8cf580-14f8-4d1f-b17a-79f147a9fcd2']
            ];
            foreach ($status_tables as $key => $value) {
                # code...
                Statustables::create($value);
            }
    }
}
