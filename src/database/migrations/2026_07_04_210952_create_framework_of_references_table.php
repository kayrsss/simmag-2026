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
        Schema::create('framework_of_references', function (Blueprint $table) {
            $table->id();
           $table->foreignId('internship_id')
                ->constrained('internships')
                ->cascadeOnDelete();

            $table->unsignedInteger('version')->default(1);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_end_date')->nullable();
            $table->longText('work_plan')->nullable();
            $table->longText('ownership_clause')->nullable();
            $table->longText('confidentiality_clause')->nullable();
            $table->longText('remuneration_clause')->nullable();
            $table->string('file_path')->nullable();

            $table->string('status')->default('Draft');

            $table->timestamp('field_supervisor_approved_at')->nullable();
            $table->timestamp('lecturer_approved_at')->nullable();

            $table->text('field_supervisor_notes')->nullable();
            $table->text('lecturer_notes')->nullable();

            $table->foreignId('previous_version_id')
                ->nullable()
                ->constrained('framework_of_references')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['internship_id', 'status']);
            $table->index('version');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('framework_of_references');
    }
};
