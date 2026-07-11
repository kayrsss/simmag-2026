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
        Schema::create('siakad_sync_logs', function (Blueprint $table) {
            $table->id();
             $table->string('sync_type')->default('Manual');
            $table->string('status')->default('Pending');

            $table->unsignedInteger('total_inserted')->default(0);
            $table->unsignedInteger('total_updated')->default(0);
            $table->unsignedInteger('total_failed')->default(0);

            $table->longText('message')->nullable();

            $table->foreignId('executed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->index(['sync_type', 'status']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siakad_sync_logs');
    }
};
