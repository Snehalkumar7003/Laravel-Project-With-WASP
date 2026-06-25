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
        Schema::create('mst_users', function (Blueprint $table) {

            $table->increments('mst_users_id');

            $table->string('username', 150)->nullable();
            $table->string('profile_photo', 150)->nullable();
            $table->string('mobile', 13)->nullable();

            $table->string('email', 75)->nullable()->unique();

            $table->string('password', 255)->nullable();

            $table->unsignedInteger('mst_roles_id')->nullable();

            $table->longText('refresh_token')->nullable();

            $table->timestamp('auth_token_expirty')->nullable();

            $table->boolean('is_first_login')->default(true);

            $table->timestamp('create_date')->useCurrent();

            $table->boolean('is_active')->default(true);

            $table->boolean('is_delete')->default(false);

            $table->timestamp('last_update')->nullable();

            $table->timestamp('last_login')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Session Management
            |--------------------------------------------------------------------------
            */

            $table->string('session_id')->nullable();

            $table->string('device_fingerprint')->nullable();

            $table->string('last_ip_address', 50)->nullable();

            $table->text('last_user_agent')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Password Policy
            |--------------------------------------------------------------------------
            */

            $table->dateTime('password_changed_at')->nullable();

            $table->boolean('force_password_change')->default(true);

            /*
            |--------------------------------------------------------------------------
            | Login OTP
            |--------------------------------------------------------------------------
            */

            $table->string('otp')->nullable();

            $table->dateTime('otp_expiry')->nullable();

            $table->integer('otp_attempts')->default(0);

            $table->integer('otp_resend_count')->default(0);

            $table->dateTime('otp_last_sent_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('mst_roles_id');
            $table->index('is_active');
            $table->index('is_delete');

        });

        // Uncomment if using InnoDB
        Schema::table('mst_users', function (Blueprint $table) {
            $table->foreign('mst_roles_id')
                  ->references('mst_roles_id')
                  ->on('mst_roles')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_users');
    }
};