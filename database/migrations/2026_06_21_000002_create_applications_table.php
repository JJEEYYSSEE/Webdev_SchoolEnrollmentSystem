<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Grade 11 admission application. One per user. Fields follow DepEd's
     * Basic Education Enrollment Form / Learner Information.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // draft (filling wizard) → pending (submitted) → invalid | qualified
            $table->string('status')->default('draft');
            $table->unsignedTinyInteger('current_step')->default(1);

            // ── Personal information ──
            $table->string('lrn', 12)->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('extension_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('sex')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('mother_tongue')->nullable();
            $table->string('religion')->nullable();
            $table->boolean('is_ip')->default(false);
            $table->string('ip_community')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->string('disability_type')->nullable();
            $table->boolean('is_4ps')->default(false);
            $table->string('household_id')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();

            // ── Address ──
            $table->string('current_address')->nullable();
            $table->string('current_barangay')->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_province')->nullable();
            $table->string('current_zip')->nullable();
            $table->boolean('permanent_same')->default(true);
            $table->string('permanent_address')->nullable();
            $table->string('permanent_barangay')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_province')->nullable();
            $table->string('permanent_zip')->nullable();

            // ── Parents / guardian ──
            $table->string('father_name')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_contact')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_contact')->nullable();

            // ── Educational background ──
            $table->string('jhs_name')->nullable();
            $table->string('jhs_school_id')->nullable();
            $table->string('jhs_year_graduated')->nullable();
            $table->decimal('general_average', 5, 2)->nullable();
            $table->string('elementary_name')->nullable();
            $table->string('elementary_year_graduated')->nullable();
            $table->boolean('is_returning')->default(false);
            $table->boolean('is_transferee')->default(false);
            $table->string('previous_school')->nullable();

            // ── Academic ──
            $table->foreignId('strand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('grade_level')->default('11');

            // ── Review ──
            $table->foreignId('reviewed_by')->nullable()->constrained('registrars')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
