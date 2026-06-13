<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
   
        Schema::create('pengaturan_situs', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 120)->unique();
            $table->text('nilai')->nullable();
            $table->timestamps();
        });
    }
};