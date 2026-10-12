<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->date('pet_birth')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('pets')->whereNull('pet_birth')->exists()) {
            throw new RuntimeException('Cannot require pet_birth while pets have an unknown birth date.');
        }

        Schema::table('pets', function (Blueprint $table) {
            $table->date('pet_birth')->nullable(false)->change();
        });
    }
};
