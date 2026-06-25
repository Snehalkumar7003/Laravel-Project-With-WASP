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
        Schema::create('tbl_email_logs', function (Blueprint $table) {

            $table->bigIncrements('tbl_email_logs_id');

            $table->unsignedBigInteger('mst_users_id')->nullable();

            $table->string('email_to', 255);

            $table->string('email_subject', 255);

            $table->string('email_template', 100);

            $table->longText('email_body')->nullable();

            $table->enum('status', [
                'PENDING',
                'SENT',
                'FAILED'
            ])->default('PENDING');

            $table->text('error_message')->nullable();

            $table->string('provider', 100)->default('RAIDS SMTP');

            $table->dateTime('sent_at')->nullable();

            $table->timestamp('create_date')->useCurrent();

            $table->timestamp('update_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('mst_users_id');
            $table->index('status');
            $table->index('email_template');
            $table->index('sent_at');

        });

        // Uncomment if using InnoDB
        Schema::table('tbl_email_logs', function (Blueprint $table) {
            $table->foreign('mst_users_id')
                  ->references('mst_users_id')
                  ->on('mst_users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_email_logs');
    }
};