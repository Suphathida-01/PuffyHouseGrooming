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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->date('booking_date');
            $table->time('booking_start_time');
            $table->time('booking_end_time');
            $table->decimal('booking_total_price', 8, 2);
            $table->text('booking_notes')->nullable();
            $table->enum('booking_status', ['pending', 'cancelled', 'completed'])->default('pending');
            

            $table->foreignId('customer_id')->constrained('customers', 'customer_id');
            $table->foreignId('admin_id')->constrained('admins', 'admin_id');
            $table->foreignId('service_id')->constrained('services', 'service_id');
            $table->foreignId('pet_id')->constrained('pets');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
