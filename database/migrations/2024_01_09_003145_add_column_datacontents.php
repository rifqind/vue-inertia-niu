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
            // drop old datacontents
            $table->dropColumn('label');
            // id table
            $table->unsignedBigInteger('id_tabel')->default(1);
            $table->foreign('id_tabel')->references('id')->on('tabels')->onUpdate('CASCADE');
            // id rows
            $table->unsignedBigInteger('id_row')->default(1);
            $table->foreign('id_row')->references('id')->on('rows')->onUpdate('CASCADE');
            // id columns
            $table->unsignedBigInteger('id_column')->default(1);
            $table->foreign('id_column')->references('id')->on('columns')->onUpdate('CASCADE');
            // id turunan tahun
            $table->unsignedBigInteger('id_turtahun')->default(1);
            $table->foreign('id_turtahun')->references('id')->on('turtahuns')->onUpdate('CASCADE');
            // tahun
            $table->year('tahun')->default('2023');
            // id regions
            $table->unsignedBigInteger('id_region')->default(1)->nullable();
            $table->foreign('id_region')->references('id')->on('regions')
                ->onUpdate('CASCADE')
                ->onDelete('SET NULL');
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
