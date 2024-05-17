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
        Schema::create('master_kabupaten', function (Blueprint $table) {
            // create columns
            $table->string('prov', 2);
            $table->string('kab', 2);

            // create label
            $table->string('label')->default('Sulawesi Utara');

            // set foreign key
            $table->foreign('prov')->references('prov')->on('master_provinsi')->onUpdate('CASCADE');
            //    $table->foreign('kab')->references('kab')->on('master_kabupaten')->onUpdate('CASCADE');

            $table->primary(['prov', 'kab']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kabupaten');
    }
};
