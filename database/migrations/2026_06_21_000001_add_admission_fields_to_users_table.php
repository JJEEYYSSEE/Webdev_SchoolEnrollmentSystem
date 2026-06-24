<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Admission fields: School ID is issued only when the registrar qualifies the applicant.
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('email');
            $table->string('school_id')->nullable()->unique()->after('birthdate');
            $table->boolean('must_change_password')->default(false)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['school_id']);
            $table->dropColumn(['birthdate', 'school_id', 'must_change_password']);
        });
    }
};
