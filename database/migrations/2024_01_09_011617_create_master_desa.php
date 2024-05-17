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
        Schema::create('master_desa', function (Blueprint $table) {
            // create columns
            $table->string('prov', 2);
            $table->string('kab', 2);
            $table->string('kec', 3);
            $table->string('desa', 3);
            // create label
            $table->string('label');
            // set foreign key
            $table->foreign(['prov', 'kab', 'kec'])->references(['prov', 'kab', 'kec'])->on('master_kecamatan')->onUpdate('CASCADE');


            $table->primary(['prov', 'kab', 'kec', 'desa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_desa');
    }
};
