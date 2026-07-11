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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
             $table->foreignId('internship_id')
                ->constrained('internships')
                ->cascadeOnDelete();

            $table->foreignId('lecturer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('consultation_date')->nullable();
            $table->string('topic');
            $table->longText('notes')->nullable();
            $table->longText('follow_up')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('status')->default('Diajukan');

            $table->timestamps();

            $table->index(['student_id', 'lecturer_id']);
            $table->index(['internship_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
