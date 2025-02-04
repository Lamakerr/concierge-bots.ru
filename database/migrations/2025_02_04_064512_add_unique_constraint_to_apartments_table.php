<?php

use Illuminate\Database\Migrations\Migration;
   use Illuminate\Database\Schema\Blueprint;
   use Illuminate\Support\Facades\Schema;

   class AddUniqueConstraintToApartmentsTable extends Migration
   {
       public function up()
       {
           Schema::table('apartments', function (Blueprint $table) {
               $table->unique(['number', 'house_id'], 'unique_apartment_house');
           });
       }

       public function down()
       {
           Schema::table('apartments', function (Blueprint $table) {
               $table->dropUnique('unique_apartment_house');
           });
       }
   }
