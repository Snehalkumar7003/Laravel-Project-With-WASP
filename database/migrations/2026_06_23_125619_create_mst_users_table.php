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

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->increments('mst_users_id');

            /*
            |--------------------------------------------------------------------------
            | User Information
            |--------------------------------------------------------------------------
            */

            $table->string('username', 150)->nullable();

            $table->string('profile_photo', 150)->nullable();

            $table->string('mobile', 13)->nullable();

            $table->string('email', 75)->nullable()->unique();

            $table->string('password', 255)->nullable();

            $table->unsignedInteger('mst_roles_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            $table->longText('refresh_token')->nullable();

            $table->timestamp('auth_token_expirty')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Password Policy
            |--------------------------------------------------------------------------
            */

            $table->tinyInteger('is_first_login')->default(1);

            $table->dateTime('password_changed_at')->nullable();

            $table->tinyInteger('force_password_change')->default(1);

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->timestamp('create_date')->useCurrent();

            $table->timestamp('last_update')->nullable();

            $table->timestamp('last_login')->nullable();

            $table->tinyInteger('is_active')->default(1);

            $table->tinyInteger('is_delete')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Session Security
            |--------------------------------------------------------------------------
            */

            $table->string('session_id', 255)->nullable();

            $table->string('device_fingerprint', 255)->nullable();

            $table->string('last_ip_address', 50)->nullable();

            $table->text('last_user_agent')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Login OTP
            |--------------------------------------------------------------------------
            */

            $table->string('otp', 255)->nullable();

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

            $table->index('session_id');

            $table->index('device_fingerprint');

            $table->index('otp_expiry');

            $table->index('otp_last_sent_at');

        });

        /*
        |--------------------------------------------------------------------------
        | Foreign Key (Only if using InnoDB)
        |--------------------------------------------------------------------------
        */

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
        // Uncomment if foreign key is enabled.
        Schema::table('mst_users', function (Blueprint $table) {
            $table->dropForeign(['mst_roles_id']);
        });
        Schema::dropIfExists('mst_users');
    }
};