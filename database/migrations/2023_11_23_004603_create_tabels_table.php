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
        Schema::create('tabels', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->string('label');
            $table->string('unit');
            $table->integer('id_dinas');
            $table->integer('id_subjek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabels');
    }
};
