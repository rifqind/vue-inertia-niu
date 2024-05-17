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
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->string('noHp', 12)->after('email')->default('081234567810');
            $blueprint->string('role')->after('noHp')->default('produsen');
            $blueprint->integer('id_dinas')->after('role')->default('1');
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
