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
        Schema::table('datacontents', function (Blueprint $table) {
            $table->dropColumn('id_region');
            $table->string('wilayah_fullcode', 10)->nullable();
            $table->foreign('wilayah_fullcode')->references('wilayah_fullcode')->on('master_wilayah')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
