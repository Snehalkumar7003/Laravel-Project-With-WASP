<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_roles', function (Blueprint $table) {

            $table->increments('mst_roles_id');

            $table->string('role_name', 100);

            $table->timestamp('create_date')->useCurrent();

            $table->boolean('is_active')->default(true);

            $table->boolean('is_delete')->default(false);

            $table->timestamp('last_update')->nullable();

            $table->index('is_active');
            $table->index('is_delete');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_roles');
    }
};
