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
        Schema::create('row_orders', function (Blueprint $table) {
            $table->id();
            $table->string('id_statustabel', 36);
            $table->string('orders');
            $table->foreign('id_statustabel')->references('id')->on('statustables')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('row_orders');
    }
};
