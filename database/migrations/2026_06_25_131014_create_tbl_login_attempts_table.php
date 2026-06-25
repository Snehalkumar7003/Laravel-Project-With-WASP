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
        Schema::create('tbl_login_attempts', function (Blueprint $table) {

            $table->bigIncrements('tbl_login_attempts_id');

            $table->string('username', 255);

            $table->string('ip_address', 45)->nullable();

            $table->boolean('is_success')->default(false);

            $table->timestamp('create_date')->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('username', 'idx_username');
            $table->index('ip_address', 'idx_ip_address');
            $table->index('create_date', 'idx_create_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_login_attempts');
    }
};