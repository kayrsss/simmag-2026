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
        Schema::create('lecturer_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')
                ->constrained('internships')
                ->cascadeOnDelete();

            $table->foreignId('evaluator_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('consistency_score')->default(0);
            $table->unsignedTinyInteger('logbook_completeness_score')->default(0);
            $table->unsignedTinyInteger('neatness_score')->default(0);
            $table->unsignedTinyInteger('content_completeness_score')->default(0);
            $table->unsignedTinyInteger('writing_flow_score')->default(0);
            $table->unsignedTinyInteger('grammar_score')->default(0);

            $table->decimal('overall_score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('assessed_at')->nullable();

            $table->timestamps();

            $table->unique('internship_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_assessments');
    }
};
