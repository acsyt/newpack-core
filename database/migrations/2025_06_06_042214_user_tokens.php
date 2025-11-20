<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->text('token');
            $table->string('type')->comment('password_reset, email_verification, 2fa_backup, etc');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('resettable_id')->nullable();
            $table->string('resettable_type')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'type', 'expires_at'], 'user_tokens_email_type_expires');
            $table->index(['resettable_type', 'resettable_id'], 'user_tokens_resettable');
            $table->index(['type', 'expires_at'], 'user_tokens_type_expires');
            $table->index(['email', 'type', 'used'], 'user_tokens_email_type_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
    }
};
