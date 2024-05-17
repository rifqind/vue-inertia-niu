<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->foreign(['prov', 'kab', 'kec', 'desa'])->references(['prov', 'kab', 'kec', 'desa'])->on('master_desa')->onUpdate('CASCADE');
            $table->primary('wilayah_fullcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
