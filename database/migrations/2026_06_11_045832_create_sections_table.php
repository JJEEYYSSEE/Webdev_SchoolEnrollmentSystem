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
            $table->foreignId('strand_id')->constrained()->onDelete('restrict');
            $table->foreignId('school_year_id')->constrained()->onDelete('restrict');
            $table->enum('grade_level', ['11', '12']);
            $table->enum('semester', ['1st', '2nd']);
            $table->string('section_name', 50);      
            $table->enum('time_period', ['AM', 'PM']);
            $table->integer('max_capacity')->default(40);
            $table->timestamps();
            $table->unique(
                ['strand_id', 'school_year_id', 'grade_level', 'semester', 'section_name'],
                'sections_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
