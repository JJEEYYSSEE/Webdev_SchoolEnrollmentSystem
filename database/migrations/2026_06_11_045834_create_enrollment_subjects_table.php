<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('restrict');
            $table->decimal('grade', 4, 2)->nullable();
            $table->enum('status', ['enrolled', 'passed', 'failed', 'dropped'])->default('enrolled');
            $table->timestamps();

            $table->unique(['enrollment_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_subjects');
    }
};
