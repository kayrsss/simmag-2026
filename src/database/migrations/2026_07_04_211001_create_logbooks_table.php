<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table): void {
            $table->id();

            $table
                ->foreignId('internship_id')
                ->nullable()
                ->constrained('internships')
                ->cascadeOnDelete();

            $table
                ->foreignId('framework_of_reference_id')
                ->nullable()
                ->constrained('framework_of_references')
                ->nullOnDelete();

            $table
                ->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('activity_date');

            $table->text('activity');

            $table
                ->string('evidence_name')
                ->nullable();

            $table
                ->string('evidence_path')
                ->nullable();

            $table
                ->string('status', 50)
                ->default('Draft')
                ->index();

            $table
                ->text('review_note')
                ->nullable();

            $table
                ->timestamp('submitted_at')
                ->nullable();

            $table
                ->timestamp('validated_at')
                ->nullable();

            $table
                ->foreignId('validated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                [
                    'student_id',
                    'activity_date',
                ],
                'logbooks_student_activity_date_index'
            );

            $table->index(
                [
                    'internship_id',
                    'activity_date',
                ],
                'logbooks_internship_activity_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};