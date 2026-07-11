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
                $table->string('nim')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('users', 'identifier')) {
                $table->string('identifier')->nullable()->unique()->after('username');
            }

            if (! Schema::hasColumn('users', 'program_study_id')) {
                $table->foreignId('program_study_id')->nullable()->after('identifier')->constrained('program_studies')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'institution_name')) {
                $table->string('institution_name')->nullable()->after('program_study_id');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('institution_name');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'program_study_id')) {
                $table->dropConstrainedForeignId('program_study_id');
            }

            foreach (['nim', 'username', 'identifier', 'institution_name', 'role', 'is_active'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};