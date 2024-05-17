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
        Schema::table('statustables', function (Blueprint $table) {
            $table->integer('edited_by');
        });
        Schema::table('metadata_variabel_status', function (Blueprint $table) {
            $table->integer('edited_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('statustables', function (Blueprint $table) {
            $table->dropColumn('edited_by');
        });
        Schema::table('metadata_variabel_status', function (Blueprint $table) {
            $table->dropColumn('edited_by');
        });
    }
};
