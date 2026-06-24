<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_year_id')->constrained()->onDelete('restrict');
            $table->enum('semester', ['1st', '2nd']);
            $table->decimal('gpa', 4, 2)->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'school_year_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_records');
    }
};
