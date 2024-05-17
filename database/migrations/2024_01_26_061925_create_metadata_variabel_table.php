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
        Schema::create('metadata_variabel', function (Blueprint $table) {
            $table->id();
            $table->integer('id_tabel');
            $table->string('r101');
            $table->text('r102');
            $table->text('r103');
            $table->text('r104');
            $table->string('r105');
            $table->string('r106');
            $table->string('r107');
            $table->string('r108');
            $table->string('r109');
            $table->string('r110');
            $table->string('r111');
            $table->string('r112',1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metadata_variabel');
    }
};
