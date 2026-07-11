<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            if (! Schema::hasColumn('internships', 'student_name')) {
                $table->string('student_name')->nullable()->after('student_id');
            }

            if (! Schema::hasColumn('internships', 'student_nim')) {
                $table->string('student_nim')->nullable()->after('student_name');
            }

            if (! Schema::hasColumn('internships', 'university_name')) {
                $table->string('university_name')->nullable()->after('student_nim');
            }

            if (! Schema::hasColumn('internships', 'lecturer_name')) {
                $table->string('lecturer_name')->nullable()->after('supervisor_lecturer_id');
            }

            if (! Schema::hasColumn('internships', 'lecturer_identifier')) {
                $table->string('lecturer_identifier')->nullable()->after('lecturer_name');
            }

            if (! Schema::hasColumn('internships', 'company_name')) {
                $table->string('company_name')->nullable()->after('company_id');
            }

            if (! Schema::hasColumn('internships', 'submitted_by')) {
                $table->foreignId('submitted_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('internships', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('submitted_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            if (Schema::hasColumn('internships', 'submitted_by')) {
                $table->dropConstrainedForeignId('submitted_by');
            }

            foreach ([
                'submitted_at',
                'company_name',
                'lecturer_identifier',
                'lecturer_name',
                'university_name',
                'student_nim',
                'student_name',
            ] as $column) {
                if (Schema::hasColumn('internships', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
