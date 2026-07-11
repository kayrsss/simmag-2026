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
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('period_id')
                ->constrained('periods')
                ->cascadeOnDelete();

            $table->foreignId('supervisor_lecturer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('company_profiles')
                ->nullOnDelete();

            $table->string('field_supervisor_name')->nullable();
            $table->string('field_supervisor_position')->nullable();
            $table->string('field_supervisor_phone')->nullable();
            $table->string('field_supervisor_email')->nullable();

            $table->string('status')->default('Draft');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'period_id']);
            $table->index('supervisor_lecturer_id');
            $table->index('status');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
