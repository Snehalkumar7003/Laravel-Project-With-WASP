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
        Schema::create('tbl_password_history', function (Blueprint $table) {

            $table->bigIncrements('tbl_password_history_id');

            $table->unsignedBigInteger('mst_users_id');

            $table->string('password', 255);

            $table->timestamp('create_date')->useCurrent();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('mst_users_id');

            // Uncomment if using InnoDB
            $table->foreign('mst_users_id')
                  ->references('mst_users_id')
                  ->on('mst_users')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_password_history');
    }
};