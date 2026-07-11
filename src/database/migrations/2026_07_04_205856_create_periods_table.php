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
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
           $table->string('academic_year', 20);
            $table->string('semester', 30);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('Draft');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['academic_year', 'semester']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};
