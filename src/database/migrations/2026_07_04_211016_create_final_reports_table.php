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
        Schema::create('final_reports', function (Blueprint $table) {
            $table->id();
          $table->foreignId('internship_id')
                ->constrained('internships')
                ->cascadeOnDelete();

            $table->string('file_path');
            $table->unsignedInteger('word_count')->nullable();

            $table->string('status')->default('Menunggu_Review');
            $table->text('revision_notes')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['internship_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_reports');
    }
};
