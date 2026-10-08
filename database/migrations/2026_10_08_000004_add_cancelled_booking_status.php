<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_status', ['pending', 'confirmed', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('bookings')->where('booking_status', 'cancelled')->update(['booking_status' => 'pending']);

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_status', ['pending', 'confirmed', 'completed'])
                ->default('pending')
                ->change();
        });
    }
};
