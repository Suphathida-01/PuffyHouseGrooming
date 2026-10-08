<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('bookings', 'booking_notes')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->text('booking_notes')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'booking_notes')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('booking_notes');
            });
        }
    }
};
