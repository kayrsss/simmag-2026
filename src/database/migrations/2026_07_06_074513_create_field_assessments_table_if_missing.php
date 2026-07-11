<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('field_assessments')) {
            Schema::create('field_assessments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('internship_id')
                    ->constrained('internships')
                    ->cascadeOnDelete();

                $table->foreignId('evaluator_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->unsignedTinyInteger('discipline_score')->default(0);
                $table->unsignedTinyInteger('initiative_score')->default(0);
                $table->unsignedTinyInteger('teamwork_score')->default(0);
                $table->unsignedTinyInteger('communication_score')->default(0);
                $table->unsignedTinyInteger('adaptability_score')->default(0);
                $table->unsignedTinyInteger('diligence_score')->default(0);
                $table->unsignedTinyInteger('appearance_score')->default(0);
                $table->unsignedTinyInteger('honesty_score')->default(0);
                $table->unsignedTinyInteger('critical_thinking_score')->default(0);
                $table->unsignedTinyInteger('responsibility_score')->default(0);

                $table->decimal('overall_score', 5, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamp('assessed_at')->nullable();

                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lecturer_assessments')) {
            Schema::create('lecturer_assessments', function (Blueprint $table) {
                $table->id();

                $table->foreignId('internship_id')
                    ->constrained('internships')
                    ->cascadeOnDelete();

                $table->foreignId('evaluator_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

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
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('field_assessments');
    }
};