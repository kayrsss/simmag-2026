<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('users', 'nidn')) {
                $table->string('nidn')->nullable()->unique()->after('nim');
            }

            if (! Schema::hasColumn('users', 'nip')) {
                $table->string('nip')->nullable()->unique()->after('nidn');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('nip');
            }

            if (! Schema::hasColumn('users', 'program_study_id')) {
                $table->foreignId('program_study_id')
                    ->nullable()
                    ->after('phone')
                    ->constrained('program_studies')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('program_study_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'program_study_id')) {
                $table->dropConstrainedForeignId('program_study_id');
            }

            $columns = ['nim', 'nidn', 'nip', 'phone', 'is_active'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};