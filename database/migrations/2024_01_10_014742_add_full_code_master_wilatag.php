<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('master_wilayah', function (Blueprint $table) {
            $table->string('wilayah_fullcode', 10)->first()->default('7100000000');
        });
        DB::statement("UPDATE master_wilayah SET wilayah_fullcode = CONCAT(prov, kab, kec,desa)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
