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
        Schema::create('residents', function (Blueprint $table) {
            $table->id()->from(1001);
            $table->string('telegram_username')->unique();
            $table->string('chat_id')->unique()->nullable();
            $table->string('name');
            $table->string('phone_number')->unique();
            $table->foreignId('resident_role_id')->constrained()->onDelete('cascade'); 
            $table->boolean('intercom_notices_agreement')->default(true);
            $table->boolean('danger_notices_agreement')->default(true);
            $table->string('status')->default('inactive');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
