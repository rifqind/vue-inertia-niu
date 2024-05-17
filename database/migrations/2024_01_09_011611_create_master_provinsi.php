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
        Schema::create('master_provinsi', function (Blueprint $table) {
            // create columns
            $table->string('prov', 2);

            // create label
            $table->string('label');


            // set primary key
            $table->primary('prov');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_provinsi');
    }
};
