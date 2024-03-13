<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'niu',
                'name' => 'HE WHO REMAINS SOLO',
                'email' => 'niu@mail.com',
                'noHp' => '081234567810',
                'role' => 'admin','id_dinas' => 17, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$sX484qzX9uE8iZyOuTR6bOI5flv4YiHpQxh7KBUy8OFKteIU5Bw.2', 
                'remember_token' => NULL, 
                'created_at' => '2023-11-27 21:46:08', 
                'updated_at' => '2024-02-04 20:59:59'
            ],
            [
                'username' => 'rifqind',
                'name' => 'Muhammad Rifqi Mubarak',
                'email' => 'ryuuzumaru@gmail.com',
                'noHp' => '081227198892',
                'role' => 'produsen','id_dinas' => 4, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$VAPARh.HEnPMi3wdGBoBeuQxfs/TmjPDuqlspm4E4kPmp8PgsiUnS', 
                'remember_token' => NULL, 
                'created_at' => '2023-11-28 23:30:37', 
                'updated_at' => '2024-02-06 22:27:07'
            ],
            [
                'username' => 'krakenshits',
                'name' => 'DRAGON',
                'email' => 'dragon@mail.com',
                'noHp' => '081234567810',
                'role' => 'produsen','id_dinas' => 1, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$RzQse/LA99/ETm8kDNeOrexyS39XXGS3W01FvzqngmEroxK5NH7Ym', 
                'remember_token' => NULL, 
                'created_at' => '2023-11-29 20:04:16', 
                'updated_at' => '2024-01-05 01:12:37'
            ],
            [
                'username' => 'dragonoid',
                'name' => 'DRAGON',
                'email' => 'dragona@mail.com',
                'noHp' => '081234567810',
                'role' => 'produsen','id_dinas' => 1, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$5l3B8m962tz0Ly9UMK.urOwcUhb3wG/gvULf4TM8BXQPuovtZaEuu', 
                'remember_token' => NULL, 
                'created_at' => '2023-11-29 20:07:58', 
                'updated_at' => '2024-01-08 18:15:18'
            ],
            [
                'username' => 'alioth',
                'name' => 'Alioth',
                'email' => 'alioth@mail.com',
                'noHp' => '081234567893',
                'role' => 'produsen','id_dinas' => 3, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$Vuh7mCvyfBTMcdtaBTslVukdNo8tWNW13cj/tp5hZc6k2FhAd8pFe', 
                'remember_token' => NULL, 
                'created_at' => '2023-11-29 20:38:39', 
                'updated_at' => '2024-01-23 19:07:47'
            ],
            [
                'username' => 'annihilus',
                'name' => 'annihilus',
                'email' => 'hilus@mail.com',
                'noHp' => '0812345633',
                'role' => 'produsen','id_dinas' => 5, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$wIDfxV8j/TXVXMSr6h2aqebE/lkEAnoXiQdnByrR.wZySpPu0ko5O', 
                'remember_token' => NULL, 
                'created_at' => '2023-12-03 22:28:42', 
                'updated_at' => '2024-01-08 19:04:59'
            ],
            [
                'username' => 'xynder',
                'name' => 'Xynder Xoloith',
                'email' => 'xynder@mail.com',
                'noHp' => '0812345633',
                'role' => 'admin','id_dinas' => 6, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$QFPpdpElL8fxvcxR7E/0.uJFrTDVPySmmtV2A4hOH76k7nPPPtq1K', 
                'remember_token' => NULL, 
                'created_at' => '2023-12-03 22:56:00', 
                'updated_at' => '2024-01-23 19:44:16'
            ],
            [
                'username' => 'Zayn',
                'name' => 'Xynder Xoloith',
                'email' => 'zayn@mail.com',
                'noHp' => '0812345633',
                'role' => 'produsen','id_dinas' => 7, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$Unn.DLuilX3IM7BQ0zBHbeDErfUBiTPIcMrsVHyjma7kcq6GOLEKu', 
                'remember_token' => NULL, 
                'created_at' => '2023-12-03 22:57:34', 
                'updated_at' => '2024-01-22 17:12:16'
            ],
            [
                'username' => 'joyboy',
                'name' => 'Monkey D. Luffy',
                'email' => 'joyoboyo@mail.com',
                'noHp' => '081234563334',
                'role' => 'produsen','id_dinas' => 9, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$yhQUIt6yCcX5L5UfatlRoeQvjrU1Lb/vPOYFeZdBHkgwQLxmAebMe', 
                'remember_token' => NULL,
                'created_at' => '2024-01-08 18:09:54',
                'updated_at' => '2024-01-08 18:09:54'
            ],
            [
                'username' => 'momo',
                'name' => 'Kozuki Momonosuke',
                'email' => 'momo@mail.com',
                'noHp' => '087955463254',
                'role' => 'produsen','id_dinas' => 13, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$ehpgc80BQppLoS3xtz9NpeuanyDUv6KwPQqQ154W00Fhnev5UshVG', 
                'remember_token' => NULL,
                'created_at' => '2024-01-10 16:35:38',
                'updated_at' => '2024-01-10 16:35:38'
            ],
            [
                'username' => 'yamato',
                'name' => 'Yamato No Bocchan',
                'email' => 'yamato@mail.com',
                'noHp' => '085412548974',
                'role' => 'produsen','id_dinas' => 1, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$P6c9P.avgKgxIVRbsY4FKOMkNJeaY3b9KtHmNOonywceOwbsZe2l.', 
                'remember_token' => NULL,
                'created_at' => '2024-01-10 23:15:00',
                'updated_at' => '2024-01-10 23:15:00'
            ],
            [
                'username' => 'rei',
                'name' => 'Naomi Rei',
                'email' => 'rei@mail.com',
                'noHp' => '081227198892',
                'role' => 'produsen','id_dinas' => 7, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$EvLfOumILkxUli0kKmI9luPd7qUdXaK9qzVFxOSL7bB7Gi8MQd2gy', 
                'remember_token' => NULL,
                'created_at' => '2024-01-16 22:27:38',
                'updated_at' => '2024-01-16 22:27:38'
            ],
            [
                'username' => 'kominfo7100',
                'name' => 'Justinus Laksana',
                'email' => 'kominfo7100@mail.com',
                'noHp' => '089825478745',
                'role' => 'kominfo','id_dinas' => 15, 
                'email_verified_at' => NULL,
                'password' => '$2y$12$0eNVsvB204BWyGFWcjveOu5HEsVfA67GQ6Ck5vmy/3YmawAmCOBUu', 
                'remember_token' => NULL,
                'created_at' => '2024-01-22 17:21:13',
                'updated_at' => '2024-01-23 20:02:18'
            ],
            [
                'username' => 'dinasSosial7107',
                'name' => 'Tim Dinas Sosial',
                'email' => 'dinsos7107@gov.id',
                'noHp' => '089825478745',
                'role' => 'produsen',
                'id_dinas' => 7,
                'email_verified_at' => NULL,
                'password' => '$2y$12$UHRlAWEL5ZaaUUkywIdegeGF3MYCbMRzQmGnCdydn/gu5H1.oUKhq', 
                'remember_token' => NULL,
                'created_at' => '2024-02-14 22:11:29',
                'updated_at' => '2024-02-14 22:11:29'
            ],
            [
                'username' => 'kominfo7107',
                'name' => 'Tim Dinas Kominfo 7107',
                'email' => 'kominfo7107@gov.id',
                'noHp' => '081143308525',
                'role' => 'kominfo',
                'id_dinas' => 21,
                'email_verified_at' => NULL,
                'password' => '$2y$12$rWbBJxLxRYsF8V0599xUTuyTllaYDTkQ/u/Ve8ghBCItULE5Znuw.', 
                'remember_token' => NULL,
                'created_at' => '2024-02-14 22:14:41',
                'updated_at' => '2024-02-14 22:14:41'
            ],
            [
                'username' => 'admin7107',
                'name' => 'Admin 7107',
                'email' => 'bps7107@bps.go.id',
                'noHp' => '081143308525',
                'role' => 'admin',
                'id_dinas' => 22,
                'email_verified_at' => NULL,
                'password' => '$2y$12$fHypItZGZ09XcQ51Ln7VpO2JoNWzRzYr2VXMJn3Vs4tIUe7Wu0KT.', 
                'remember_token' => NULL,
                'created_at' => '2024-02-14 22:15:55',
                'updated_at' => '2024-02-14 22:15:55'
            ],
            [
                'username' => 'kel_wanea',
                'name' => 'Tim Kelurahan Wanea',
                'email' => 'wanea@desa.go.id',
                'noHp' => '081143308525',
                'role' => 'produsen',
                'id_dinas' => 19,
                'email_verified_at' => NULL,
                'password' => '$2y$12$OsiSzKyqwH8TN2nqJUQ0H.f2cqfcYJ9wzEYe4hou3fW.uRUVkJlbK', 
                'remember_token' => NULL,
                'created_at' => '2024-02-15 23:42:10',
                'updated_at' => '2024-02-15 23:42:10'
            ],
            [
                'username' => 'kec_wanea',
                'name' => 'Tim Kecamatan Wanea',
                'email' => 'wanea@kec.go.id',
                'noHp' => '081143308525',
                'role' => 'produsen',
                'id_dinas' => 20,
                'email_verified_at' => NULL,
                'password' => '$2y$12$MsLLRiSouJ6kp7ULzDhO1./ZU6Cx0BMNUvy4OozDibTMrKx7ut1Eq', 
                'remember_token' => NULL,
                'created_at' => '2024-02-16 00:05:20',
                'updated_at' => '2024-02-16 00:05:20'
            ],
            [
                'username' => 'bps7171',
                'name' => 'Tim BPS Kota Manado',
                'email' => 'bps7171@bps.go.id',
                'noHp' => '089825478745',
                'role' => 'admin',
                'id_dinas' => 24,
                'email_verified_at' => NULL,
                'password' => '$2y$12$n20St7iOtrcw5EtrVtP.yuSGOjhJxgwf5lZFUC.7f/8BMFlNpfy1q', 
                'remember_token' => NULL,
                'created_at' => '2024-02-16 00:16:58',
                'updated_at' => '2024-02-16 00:16:58'
            ],
            [
                'username' => 'kec.tompaso',
                'name' => 'Tim Kec Tompaso',
                'email' => 'tompaso@kec.go.id',
                'noHp' => '081143308525',
                'role' => 'produsen',
                'id_dinas' => 25,
                'email_verified_at' => NULL,
                'password' => '$2y$12$OMeLrtvp2Q0VjL5B2p5wn.5iFO.iakFyw/ikq2trcHm9Q.uZpy4vm', 
                'remember_token' => NULL,
                'created_at' => '2024-02-19 16:37:26',
                'updated_at' => '2024-02-19 16:37:26'
            ],
            [
                'username' => 'desa.kamanga',
                'name' => 'Tim Desa Kamanga',
                'email' => 'kamanga@desa.go.id',
                'noHp' => '089825478745',
                'role' => 'produsen',
                'id_dinas' => 26,
                'email_verified_at' => NULL,
                'password' => '$2y$12$PQtqsOfLC255SHVJ4CTxt.K1MDiLoDfPqOKBOQtYSfx9IMDUaDOhi', 
                'remember_token' => NULL,
                'created_at' => '2024-02-19 16:38:06',
                'updated_at' => '2024-02-19 16:38:06'
            ],
            [
                'username' => 'admin7101',
                'name' => 'Tim BPS Bolmong',
                'email' => 'bps7101@bps.go.id',
                'noHp' => '081227198892',
                'role' => 'admin',
                'id_dinas' => 28,
                'email_verified_at' => NULL,
                'password' => '$2y$12$fpHaWYlxOCMQNPO/6Z1yC.aL/d10Fem7eNN11qhz1ErUuTZOzF8K2', 
                'remember_token' => NULL,
                'created_at' => '2024-02-22 06:54:35',
                'updated_at' => '2024-02-22 06:54:35'
            ],
            [
                'username' => 'ridwanst',
                'name' => 'Ridwan Setiawan',
                'email' => 'ridwanst@bps.go.id',
                'noHp' => '081234563334',
                'role' => 'produsen',
                'id_dinas' => 17,
                'email_verified_at' => NULL,
                'password' => '$2y$12$xck0oVqraj2i6eIb9zcFf.laprc8niQp1DzsjUOPOYRgP2.Es/OZO', 
                'remember_token' => NULL,
                'created_at' => '2024-03-12 16:54:31',
                'updated_at' => '2024-03-12 16:54:31'
            ],
            [
                'username' => 'ponim',
                'name' => 'Poniman',
                'email' => 'poniman@bps.go.id',
                'noHp' => '081234567890',
                'role' => 'produsen',
                'id_dinas' => 17,
                'email_verified_at' => NULL,
                'password' => '$2y$12$mYhC9CUPjK7xDJFG5lATDe3JPD8opGz3Dyfnnu5SgobH3/x7dyG4O', 
                'remember_token' => NULL,
                'created_at' => '2024-03-12 17:04:12',
                'updated_at' => '2024-03-12 17:04:12'
            ],
            [
                'username' => 'yuliuswt',
                'name' => 'Yulius W T',
                'email' => 'wyt@bps.go.id',
                'noHp' => '089825478745',
                'role' => 'produsen',
                'id_dinas' => 17,
                'email_verified_at' => NULL,
                'password' => '$2y$12$URSJes9g6k0DpFx0LzYZDujey8ba261Xr4U37zm4WJCC/dCVGJvA2', 
                'remember_token' => NULL,
                'created_at' => '2024-03-12 17:05:58',
                'updated_at' => '2024-03-12 17:05:58'
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
