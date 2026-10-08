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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('pet_name');
            $table->date('pet_birth');
            $table->enum('pet_gender', ['male', 'female']);
            $table->decimal('pet_weight', 5, 2);
            $table->text('pet_notes')->nullable();
            $table->string('pet_image')->nullable();
            
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->cascadeOnDelete();
            $table->foreignId('pet_breed_id')->constrained('pet_breeds', 'pet_breed_id');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
