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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')
                ->constrained('internships')
                ->cascadeOnDelete();

            $table->foreignId('framework_of_reference_id')
                ->nullable()
                ->constrained('framework_of_references')
                ->nullOnDelete();

            $table->date('activity_date');
            $table->longText('description');
            $table->string('attachment_file')->nullable();

            $table->string('status')->default('Menunggu_Validasi');
            $table->text('validation_notes')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->index(['internship_id', 'activity_date']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
