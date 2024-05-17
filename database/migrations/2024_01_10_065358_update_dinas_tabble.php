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
        Schema::table('dinas', function (Blueprint $table) {
            $table->dropColumn('id_regions');

            $table->string('wilayah_fullcode', 10)->default('7100000000');
            $table->foreign('wilayah_fullcode')->references('wilayah_fullcode')->on('master_wilayah')->onUpdate('CASCADE');
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
