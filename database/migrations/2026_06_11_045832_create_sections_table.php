<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained()->onDelete('restrict');
            $table->string('section_name', 50);
            $table->string('year_level', 20);
            $table->string('course')->nullable();
            $table->string('advisor_name')->nullable();
            $table->integer('max_slots')->default(40);
            $table->integer('current_slots')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
