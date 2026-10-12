<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fold the old intermediate state into the new pending state.
        DB::table('bookings')->where('booking_status', 'confirmed')->update([
            'booking_status' => 'pending',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // The previous state cannot be reconstructed after confirmed rows
        // have been normalized to pending.
    }
};
