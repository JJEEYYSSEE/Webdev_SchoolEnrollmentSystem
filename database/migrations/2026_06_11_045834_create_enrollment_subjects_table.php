<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Snapshot of subjects auto-copied from section_subjects at enrollment time.
        // Historical record stays accurate even if section_subjects changes later.
        // grade is encoded by registrar after the semester ends.
        Schema::create('enrollment_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('restrict');
            $table->decimal('grade', 4, 2)->nullable(); // 1.00 - 5.00, null until encoded
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
