<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('year_label', 20)->unique();   // e.g. '2026-2027'
            $table->boolean('is_active')->default(false);
            $table->enum('active_semester', ['1st', '2nd'])->default('1st'); // which sem is current
            $table->boolean('is_enrollment_open')->default(false); // registrar opens/closes enrollment
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};
