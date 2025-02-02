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
        Schema::create('houses', function (Blueprint $table) {
            $table->id()->from(1001);
            $table->string('street');
            $table->integer('number')->unsigned(); 
            $table->integer('building')->nullable()->unsigned();
            $table->integer('floors')->unsigned(); 
            $table->integer('entrances')->unsigned(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
