<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Holds a pending password change awaiting email-OTP confirmation.
     * One active row per user; replaced on each new request, deleted once applied.
     */
    public function up(): void
    {
        Schema::create('password_change_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code');          // hashed 6-digit code
            $table->string('new_password');  // bcrypt hash of the requested new password
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_change_otps');
    }
};
