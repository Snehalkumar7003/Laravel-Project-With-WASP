<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::create('tbl_password_resets', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Primary Key
            |--------------------------------------------------------------------------
            */

            $table->id('tbl_password_resets_id');

            /*
            |--------------------------------------------------------------------------
            | User Information
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('mst_users_id');

            $table->string('email', 255);

            /*
            |--------------------------------------------------------------------------
            | Reset Token
            |--------------------------------------------------------------------------
            */

            $table->string('token', 255);

            /*
            |--------------------------------------------------------------------------
            | Token Expiry
            |--------------------------------------------------------------------------
            */

            $table->dateTime('expires_at');

            /*
            |--------------------------------------------------------------------------
            | Used Timestamp
            |--------------------------------------------------------------------------
            */

            $table->dateTime('used_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */

            $table->string('ip_address', 45)->nullable();

            $table->text('user_agent')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->timestamp('create_date')
                ->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('email');

            $table->index('token');

            $table->index('mst_users_id');

            $table->index('expires_at');

            $table->index('used_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_password_resets');
    }
};