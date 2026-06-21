<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-subject weekly schedule slot for a section. Populated by the schedule
     * generator from DepEd class hours + the section's AM/PM time period.
     */
    public function up(): void
    {
        Schema::table('section_subjects', function (Blueprint $table) {
            $table->string('day_of_week', 3)->nullable()->after('subject_id'); // Mon..Fri
            $table->time('start_time')->nullable()->after('day_of_week');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('room', 50)->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('section_subjects', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'start_time', 'end_time', 'room']);
        });
    }
};
