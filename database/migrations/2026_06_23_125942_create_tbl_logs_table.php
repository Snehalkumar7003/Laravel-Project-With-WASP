<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_logs', function (Blueprint $table) {

            $table->increments('tbl_logs_id');

            $table->string('module', 100)->nullable();

            $table->integer('module_id')->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->longText('inserted_data')->nullable();

            $table->longText('previous_data')->nullable();

            $table->unsignedInteger('mst_users_id')->nullable();

            $table->string('action', 50)->nullable();

            $table->longText('remarks')->nullable();

            $table->timestamp('create_date')->useCurrent();

            $table->index('mst_users_id');
            $table->index('module_id');
            $table->index('action');
            $table->index('create_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_logs');
    }
};
