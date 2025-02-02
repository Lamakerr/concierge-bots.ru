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
        Schema::create('apartment_resident_table', function (Blueprint $table) {
            $table->id()->from(1001);
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('resident_id')->constrained()->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_resident_table');
    }
};
