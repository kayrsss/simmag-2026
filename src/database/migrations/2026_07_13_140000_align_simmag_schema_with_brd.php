<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->alignUsers();
        $this->alignProgramStudies();
        $this->alignPeriods();
        $this->alignCompanyProfiles();
        $this->alignInternships();
        $this->alignFrameworksOfReference();
        $this->alignLogbooks();
        $this->alignConsultations();
        $this->alignFinalReports();
        $this->alignFieldAssessments();
        $this->alignLecturerAssessments();
        $this->alignAuditTrails();
        $this->alignAnnouncements();
        $this->alignSiakadSyncLogs();
        $this->alignNotifications();

        $this->backfillLegacyData();
    }

    public function down(): void
    {
        /*
         * Migration ini tidak menghapus tabel maupun data.
         * Rollback sengaja dikosongkan agar data magang,
         * dokumen, penilaian, dan audit trail tetap aman.
         */
    }

    private function alignUsers(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $this->addColumn(
            'users',
            'username',
            function (Blueprint $table): void {
                $table->string('username')
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'identifier',
            function (Blueprint $table): void {
                $table->string('identifier')
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'nim',
            function (Blueprint $table): void {
                $table->string('nim')
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'nidn',
            function (Blueprint $table): void {
                $table->string('nidn')
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'nip',
            function (Blueprint $table): void {
                $table->string('nip')
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'phone',
            function (Blueprint $table): void {
                $table->string('phone', 30)
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'program_study_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'program_study_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'users',
            'institution_name',
            function (Blueprint $table): void {
                $table->string(
                    'institution_name'
                )->nullable();
            }
        );

        $this->addColumn(
            'users',
            'role',
            function (Blueprint $table): void {
                $table->string('role')
                    ->nullable();
            }
        );

        $this->addColumn(
            'users',
            'is_active',
            function (Blueprint $table): void {
                $table->boolean('is_active')
                    ->default(true);
            }
        );
    }

    private function alignProgramStudies(): void
    {
        if (! Schema::hasTable('program_studies')) {
            Schema::create(
                'program_studies',
                function (Blueprint $table): void {
                    $table->id();

                    $table->string('code')
                        ->unique();

                    $table->string('name');

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'program_studies',
            'code',
            function (Blueprint $table): void {
                $table->string('code')
                    ->nullable();
            }
        );

        $this->addColumn(
            'program_studies',
            'name',
            function (Blueprint $table): void {
                $table->string('name')
                    ->nullable();
            }
        );
    }

    private function alignPeriods(): void
    {
        if (! Schema::hasTable('periods')) {
            Schema::create(
                'periods',
                function (Blueprint $table): void {
                    $table->id();

                    $table->string(
                        'academic_year'
                    );

                    $table->string('semester');

                    $table->date('start_date');

                    $table->date('end_date');

                    $table->string('status')
                        ->default('Aktif');

                    $table->boolean('is_active')
                        ->default(true);

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'periods',
            'academic_year',
            function (Blueprint $table): void {
                $table->string(
                    'academic_year'
                )->nullable();
            }
        );

        $this->addColumn(
            'periods',
            'semester',
            function (Blueprint $table): void {
                $table->string('semester')
                    ->nullable();
            }
        );

        $this->addColumn(
            'periods',
            'start_date',
            function (Blueprint $table): void {
                $table->date('start_date')
                    ->nullable();
            }
        );

        $this->addColumn(
            'periods',
            'end_date',
            function (Blueprint $table): void {
                $table->date('end_date')
                    ->nullable();
            }
        );

        $this->addColumn(
            'periods',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->default('Aktif');
            }
        );

        $this->addColumn(
            'periods',
            'is_active',
            function (Blueprint $table): void {
                $table->boolean('is_active')
                    ->default(true);
            }
        );
    }

    private function alignCompanyProfiles(): void
    {
        if (! Schema::hasTable('company_profiles')) {
            Schema::create(
                'company_profiles',
                function (Blueprint $table): void {
                    $table->id();

                    $table->string('name');

                    $table->text('address')
                        ->nullable();

                    $table->string('phone', 30)
                        ->nullable();

                    $table->string('email')
                        ->nullable();

                    $table->string('pic_name')
                        ->nullable();

                    $table->string('pic_position')
                        ->nullable();

                    $table->string('pic_phone', 30)
                        ->nullable();

                    $table->boolean('is_active')
                        ->default(true);

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'company_profiles',
            'name',
            function (Blueprint $table): void {
                $table->string('name')
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'address',
            function (Blueprint $table): void {
                $table->text('address')
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'phone',
            function (Blueprint $table): void {
                $table->string('phone', 30)
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'email',
            function (Blueprint $table): void {
                $table->string('email')
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'pic_name',
            function (Blueprint $table): void {
                $table->string('pic_name')
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'pic_position',
            function (Blueprint $table): void {
                $table->string('pic_position')
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'pic_phone',
            function (Blueprint $table): void {
                $table->string('pic_phone', 30)
                    ->nullable();
            }
        );

        $this->addColumn(
            'company_profiles',
            'is_active',
            function (Blueprint $table): void {
                $table->boolean('is_active')
                    ->default(true);
            }
        );
    }

    private function alignInternships(): void
    {
        if (! Schema::hasTable('internships')) {
            Schema::create(
                'internships',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId('student_id')
                        ->constrained('users')
                        ->cascadeOnDelete();

                    $table->foreignId('period_id')
                        ->constrained('periods')
                        ->restrictOnDelete();

                    $table->foreignId(
                        'supervisor_lecturer_id'
                    )
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();

                    $table->foreignId(
                        'field_supervisor_id'
                    )
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();

                    $table->foreignId('company_id')
                        ->nullable()
                        ->constrained(
                            'company_profiles'
                        )
                        ->nullOnDelete();

                    $table->string(
                        'field_supervisor_name'
                    )->nullable();

                    $table->string(
                        'field_supervisor_position'
                    )->nullable();

                    $table->string(
                        'field_supervisor_phone',
                        30
                    )->nullable();

                    $table->string(
                        'field_supervisor_email'
                    )->nullable();

                    $table->string('status')
                        ->default('Draft');

                    $table->date('started_at')
                        ->nullable();

                    $table->date('ended_at')
                        ->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'internships',
            'student_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'student_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'period_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'period_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'supervisor_lecturer_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'supervisor_lecturer_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'field_supervisor_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'field_supervisor_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'company_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'company_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'field_supervisor_name',
            function (Blueprint $table): void {
                $table->string(
                    'field_supervisor_name'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'field_supervisor_position',
            function (Blueprint $table): void {
                $table->string(
                    'field_supervisor_position'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'field_supervisor_phone',
            function (Blueprint $table): void {
                $table->string(
                    'field_supervisor_phone',
                    30
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'field_supervisor_email',
            function (Blueprint $table): void {
                $table->string(
                    'field_supervisor_email'
                )->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->default('Draft');
            }
        );

        $this->addColumn(
            'internships',
            'started_at',
            function (Blueprint $table): void {
                $table->date('started_at')
                    ->nullable();
            }
        );

        $this->addColumn(
            'internships',
            'ended_at',
            function (Blueprint $table): void {
                $table->date('ended_at')
                    ->nullable();
            }
        );
    }

    private function alignFrameworksOfReference(): void
    {
        if (
            ! Schema::hasTable(
                'framework_of_references'
            )
        ) {
            Schema::create(
                'framework_of_references',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'internship_id'
                    )
                        ->constrained(
                            'internships'
                        )
                        ->cascadeOnDelete();

                    $table->unsignedInteger(
                        'version'
                    )->default(1);

                    $table->string('title');

                    $table->text('description');

                    $table->date('start_date')
                        ->nullable();

                    $table->date(
                        'target_end_date'
                    )->nullable();

                    $table->longText('work_plan');

                    $table->text(
                        'ownership_clause'
                    )->nullable();

                    $table->text(
                        'confidentiality_clause'
                    )->nullable();

                    $table->text(
                        'remuneration_clause'
                    )->nullable();

                    $table->string('file_path')
                        ->nullable();

                    $table->string('status')
                        ->default('Draft');

                    $table->timestamp(
                        'submitted_at'
                    )->nullable();

                    $table->timestamp(
                        'field_supervisor_approved_at'
                    )->nullable();

                    $table->timestamp(
                        'lecturer_approved_at'
                    )->nullable();

                    $table->text(
                        'field_supervisor_notes'
                    )->nullable();

                    $table->text(
                        'lecturer_notes'
                    )->nullable();

                    $table->unsignedBigInteger(
                        'previous_version_id'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'framework_of_references',
            'internship_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'internship_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'version',
            function (Blueprint $table): void {
                $table->unsignedInteger(
                    'version'
                )->default(1);
            }
        );

        $this->addColumn(
            'framework_of_references',
            'title',
            function (Blueprint $table): void {
                $table->string('title')
                    ->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'description',
            function (Blueprint $table): void {
                $table->text('description')
                    ->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'start_date',
            function (Blueprint $table): void {
                $table->date('start_date')
                    ->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'target_end_date',
            function (Blueprint $table): void {
                $table->date(
                    'target_end_date'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'work_plan',
            function (Blueprint $table): void {
                $table->longText('work_plan')
                    ->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'ownership_clause',
            function (Blueprint $table): void {
                $table->text(
                    'ownership_clause'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'confidentiality_clause',
            function (Blueprint $table): void {
                $table->text(
                    'confidentiality_clause'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'remuneration_clause',
            function (Blueprint $table): void {
                $table->text(
                    'remuneration_clause'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'file_path',
            function (Blueprint $table): void {
                $table->string('file_path')
                    ->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->default('Draft');
            }
        );

        $this->addColumn(
            'framework_of_references',
            'submitted_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'submitted_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'field_supervisor_approved_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'field_supervisor_approved_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'lecturer_approved_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'lecturer_approved_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'field_supervisor_notes',
            function (Blueprint $table): void {
                $table->text(
                    'field_supervisor_notes'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'lecturer_notes',
            function (Blueprint $table): void {
                $table->text(
                    'lecturer_notes'
                )->nullable();
            }
        );

        $this->addColumn(
            'framework_of_references',
            'previous_version_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'previous_version_id'
                )->nullable();
            }
        );
    }

    private function alignLogbooks(): void
    {
        if (! Schema::hasTable('logbooks')) {
            Schema::create(
                'logbooks',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'internship_id'
                    )
                        ->constrained(
                            'internships'
                        )
                        ->cascadeOnDelete();

                    $table->foreignId(
                        'framework_of_reference_id'
                    )
                        ->nullable()
                        ->constrained(
                            'framework_of_references'
                        )
                        ->nullOnDelete();

                    $table->date(
                        'activity_date'
                    );

                    $table->text('description');

                    $table->decimal(
                        'duration_hours',
                        5,
                        2
                    );

                    $table->string(
                        'attachment_file'
                    )->nullable();

                    $table->string('status')
                        ->default('Draft');

                    $table->text(
                        'validation_notes'
                    )->nullable();

                    $table->foreignId(
                        'validated_by'
                    )
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();

                    $table->timestamp(
                        'validated_at'
                    )->nullable();

                    $table->timestamp(
                        'submitted_at'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'logbooks',
            'internship_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'internship_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'framework_of_reference_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'framework_of_reference_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'activity_date',
            function (Blueprint $table): void {
                $table->date('activity_date')
                    ->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'description',
            function (Blueprint $table): void {
                $table->text('description')
                    ->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'duration_hours',
            function (Blueprint $table): void {
                $table->decimal(
                    'duration_hours',
                    5,
                    2
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'attachment_file',
            function (Blueprint $table): void {
                $table->string(
                    'attachment_file'
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->default('Draft');
            }
        );

        $this->addColumn(
            'logbooks',
            'validation_notes',
            function (Blueprint $table): void {
                $table->text(
                    'validation_notes'
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'validated_by',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'validated_by'
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'validated_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'validated_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'logbooks',
            'submitted_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'submitted_at'
                )->nullable();
            }
        );
    }

    private function alignConsultations(): void
    {
        if (! Schema::hasTable('consultations')) {
            Schema::create(
                'consultations',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'internship_id'
                    )
                        ->constrained(
                            'internships'
                        )
                        ->cascadeOnDelete();

                    $table->foreignId(
                        'lecturer_id'
                    )
                        ->constrained('users')
                        ->restrictOnDelete();

                    $table->foreignId(
                        'student_id'
                    )
                        ->constrained('users')
                        ->cascadeOnDelete();

                    $table->dateTime(
                        'consultation_date'
                    )->nullable();

                    $table->string('topic');

                    $table->text('notes')
                        ->nullable();

                    $table->text('follow_up')
                        ->nullable();

                    $table->unsignedSmallInteger(
                        'duration_minutes'
                    )->nullable();

                    $table->string(
                        'meeting_link'
                    )->nullable();

                    $table->string('status')
                        ->default('Diajukan');

                    $table->timestamp(
                        'completed_at'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'consultations',
            'internship_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'internship_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'lecturer_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'lecturer_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'student_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'student_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'consultation_date',
            function (Blueprint $table): void {
                $table->dateTime(
                    'consultation_date'
                )->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'topic',
            function (Blueprint $table): void {
                $table->string('topic')
                    ->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'notes',
            function (Blueprint $table): void {
                $table->text('notes')
                    ->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'follow_up',
            function (Blueprint $table): void {
                $table->text('follow_up')
                    ->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'duration_minutes',
            function (Blueprint $table): void {
                $table->unsignedSmallInteger(
                    'duration_minutes'
                )->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'meeting_link',
            function (Blueprint $table): void {
                $table->string(
                    'meeting_link'
                )->nullable();
            }
        );

        $this->addColumn(
            'consultations',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->default('Diajukan');
            }
        );

        $this->addColumn(
            'consultations',
            'completed_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'completed_at'
                )->nullable();
            }
        );
    }

    private function alignFinalReports(): void
    {
        if (! Schema::hasTable('final_reports')) {
            Schema::create(
                'final_reports',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'internship_id'
                    )
                        ->constrained(
                            'internships'
                        )
                        ->cascadeOnDelete();

                    $table->unsignedInteger(
                        'version'
                    )->default(1);

                    $table->string('file_path');

                    $table->unsignedInteger(
                        'word_count'
                    )->nullable();

                    $table->string('status')
                        ->default(
                            'Menunggu_Review'
                        );

                    $table->text(
                        'revision_notes'
                    )->nullable();

                    $table->foreignId(
                        'reviewed_by'
                    )
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();

                    $table->timestamp(
                        'submitted_at'
                    )->nullable();

                    $table->timestamp(
                        'approved_at'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'final_reports',
            'internship_id',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'internship_id'
                )->nullable();
            }
        );

        $this->addColumn(
            'final_reports',
            'version',
            function (Blueprint $table): void {
                $table->unsignedInteger(
                    'version'
                )->default(1);
            }
        );

        $this->addColumn(
            'final_reports',
            'file_path',
            function (Blueprint $table): void {
                $table->string('file_path')
                    ->nullable();
            }
        );

        $this->addColumn(
            'final_reports',
            'word_count',
            function (Blueprint $table): void {
                $table->unsignedInteger(
                    'word_count'
                )->nullable();
            }
        );

        $this->addColumn(
            'final_reports',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->default(
                        'Menunggu_Review'
                    );
            }
        );

        $this->addColumn(
            'final_reports',
            'revision_notes',
            function (Blueprint $table): void {
                $table->text(
                    'revision_notes'
                )->nullable();
            }
        );

        $this->addColumn(
            'final_reports',
            'reviewed_by',
            function (Blueprint $table): void {
                $table->unsignedBigInteger(
                    'reviewed_by'
                )->nullable();
            }
        );

        $this->addColumn(
            'final_reports',
            'submitted_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'submitted_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'final_reports',
            'approved_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'approved_at'
                )->nullable();
            }
        );
    }

    private function alignFieldAssessments(): void
    {
        if (! Schema::hasTable('field_assessments')) {
            Schema::create(
                'field_assessments',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'internship_id'
                    )
                        ->constrained(
                            'internships'
                        )
                        ->cascadeOnDelete();

                    $table->foreignId(
                        'evaluator_id'
                    )
                        ->constrained('users')
                        ->restrictOnDelete();

                    $table->decimal(
                        'performance_satisfaction_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'discipline_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'work_spirit_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'accuracy_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'punctuality_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'priority_management_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'teamwork_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'independent_work_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'learning_willingness_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'problem_solving_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'overall_score',
                        5,
                        2
                    );

                    $table->text('notes')
                        ->nullable();

                    $table->timestamp(
                        'assessed_at'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addUnsignedBigIntegerColumn(
            'field_assessments',
            'internship_id'
        );

        $this->addUnsignedBigIntegerColumn(
            'field_assessments',
            'evaluator_id'
        );

        $scoreColumns = [
            'performance_satisfaction_score',
            'discipline_score',
            'work_spirit_score',
            'accuracy_score',
            'punctuality_score',
            'priority_management_score',
            'teamwork_score',
            'independent_work_score',
            'learning_willingness_score',
            'problem_solving_score',
            'overall_score',
        ];

        foreach ($scoreColumns as $column) {
            $this->addColumn(
                'field_assessments',
                $column,
                function (
                    Blueprint $table
                ) use ($column): void {
                    $table->decimal(
                        $column,
                        5,
                        2
                    )->nullable();
                }
            );
        }

        $this->addColumn(
            'field_assessments',
            'notes',
            function (Blueprint $table): void {
                $table->text('notes')
                    ->nullable();
            }
        );

        $this->addColumn(
            'field_assessments',
            'assessed_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'assessed_at'
                )->nullable();
            }
        );
    }

    private function alignLecturerAssessments(): void
    {
        if (
            ! Schema::hasTable(
                'lecturer_assessments'
            )
        ) {
            Schema::create(
                'lecturer_assessments',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'internship_id'
                    )
                        ->constrained(
                            'internships'
                        )
                        ->cascadeOnDelete();

                    $table->foreignId(
                        'evaluator_id'
                    )
                        ->constrained('users')
                        ->restrictOnDelete();

                    $table->decimal(
                        'consistency_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'logbook_completeness_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'neatness_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'content_completeness_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'writing_flow_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'grammar_score',
                        5,
                        2
                    );

                    $table->decimal(
                        'overall_score',
                        5,
                        2
                    );

                    $table->text('notes')
                        ->nullable();

                    $table->timestamp(
                        'assessed_at'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addUnsignedBigIntegerColumn(
            'lecturer_assessments',
            'internship_id'
        );

        $this->addUnsignedBigIntegerColumn(
            'lecturer_assessments',
            'evaluator_id'
        );

        $scoreColumns = [
            'consistency_score',
            'logbook_completeness_score',
            'neatness_score',
            'content_completeness_score',
            'writing_flow_score',
            'grammar_score',
            'overall_score',
        ];

        foreach ($scoreColumns as $column) {
            $this->addColumn(
                'lecturer_assessments',
                $column,
                function (
                    Blueprint $table
                ) use ($column): void {
                    $table->decimal(
                        $column,
                        5,
                        2
                    )->nullable();
                }
            );
        }

        $this->addColumn(
            'lecturer_assessments',
            'notes',
            function (Blueprint $table): void {
                $table->text('notes')
                    ->nullable();
            }
        );

        $this->addColumn(
            'lecturer_assessments',
            'assessed_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'assessed_at'
                )->nullable();
            }
        );
    }

    private function alignAuditTrails(): void
    {
        if (! Schema::hasTable('audit_trails')) {
            Schema::create(
                'audit_trails',
                function (Blueprint $table): void {
                    $table->id();

                    $table->foreignId(
                        'user_id'
                    )
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();

                    $table->string('action');

                    $table->string(
                        'entity_type'
                    );

                    $table->unsignedBigInteger(
                        'entity_id'
                    );

                    $table->string(
                        'previous_status'
                    )->nullable();

                    $table->string(
                        'new_status'
                    )->nullable();

                    $table->text('notes')
                        ->nullable();

                    $table->string(
                        'ip_address',
                        45
                    )->nullable();

                    $table->text(
                        'user_agent'
                    )->nullable();

                    $table->timestamp(
                        'created_at'
                    )->useCurrent();

                    $table->index([
                        'entity_type',
                        'entity_id',
                    ]);
                }
            );

            return;
        }

        $this->addUnsignedBigIntegerColumn(
            'audit_trails',
            'user_id'
        );

        $this->addColumn(
            'audit_trails',
            'action',
            function (Blueprint $table): void {
                $table->string('action')
                    ->nullable();
            }
        );

        $this->addColumn(
            'audit_trails',
            'entity_type',
            function (Blueprint $table): void {
                $table->string(
                    'entity_type'
                )->nullable();
            }
        );

        $this->addUnsignedBigIntegerColumn(
            'audit_trails',
            'entity_id'
        );

        $this->addColumn(
            'audit_trails',
            'previous_status',
            function (Blueprint $table): void {
                $table->string(
                    'previous_status'
                )->nullable();
            }
        );

        $this->addColumn(
            'audit_trails',
            'new_status',
            function (Blueprint $table): void {
                $table->string(
                    'new_status'
                )->nullable();
            }
        );

        $this->addColumn(
            'audit_trails',
            'notes',
            function (Blueprint $table): void {
                $table->text('notes')
                    ->nullable();
            }
        );

        $this->addColumn(
            'audit_trails',
            'ip_address',
            function (Blueprint $table): void {
                $table->string(
                    'ip_address',
                    45
                )->nullable();
            }
        );

        $this->addColumn(
            'audit_trails',
            'user_agent',
            function (Blueprint $table): void {
                $table->text(
                    'user_agent'
                )->nullable();
            }
        );

        $this->addColumn(
            'audit_trails',
            'created_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'created_at'
                )->nullable();
            }
        );
    }

    private function alignAnnouncements(): void
    {
        if (! Schema::hasTable('announcements')) {
            Schema::create(
                'announcements',
                function (Blueprint $table): void {
                    $table->id();

                    $table->string('title');

                    $table->longText('content');

                    $table->string('priority')
                        ->default('Biasa');

                    $table->foreignId(
                        'created_by'
                    )
                        ->constrained('users')
                        ->restrictOnDelete();

                    $table->timestamp(
                        'published_at'
                    )->nullable();

                    $table->boolean('is_active')
                        ->default(true);

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'announcements',
            'title',
            function (Blueprint $table): void {
                $table->string('title')
                    ->nullable();
            }
        );

        $this->addColumn(
            'announcements',
            'content',
            function (Blueprint $table): void {
                $table->longText('content')
                    ->nullable();
            }
        );

        $this->addColumn(
            'announcements',
            'priority',
            function (Blueprint $table): void {
                $table->string('priority')
                    ->default('Biasa');
            }
        );

        $this->addUnsignedBigIntegerColumn(
            'announcements',
            'created_by'
        );

        $this->addColumn(
            'announcements',
            'published_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'published_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'announcements',
            'is_active',
            function (Blueprint $table): void {
                $table->boolean('is_active')
                    ->default(true);
            }
        );
    }

    private function alignSiakadSyncLogs(): void
    {
        if (! Schema::hasTable('siakad_sync_logs')) {
            Schema::create(
                'siakad_sync_logs',
                function (Blueprint $table): void {
                    $table->id();

                    $table->string('sync_type');

                    $table->string('status');

                    $table->unsignedInteger(
                        'total_received'
                    )->default(0);

                    $table->unsignedInteger(
                        'total_created'
                    )->default(0);

                    $table->unsignedInteger(
                        'total_updated'
                    )->default(0);

                    $table->unsignedInteger(
                        'total_failed'
                    )->default(0);

                    $table->text('message')
                        ->nullable();

                    $table->json('payload')
                        ->nullable();

                    $table->foreignId(
                        'triggered_by'
                    )
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();

                    $table->timestamp(
                        'started_at'
                    )->nullable();

                    $table->timestamp(
                        'finished_at'
                    )->nullable();

                    $table->timestamps();
                }
            );

            return;
        }

        $this->addColumn(
            'siakad_sync_logs',
            'sync_type',
            function (Blueprint $table): void {
                $table->string('sync_type')
                    ->nullable();
            }
        );

        $this->addColumn(
            'siakad_sync_logs',
            'status',
            function (Blueprint $table): void {
                $table->string('status')
                    ->nullable();
            }
        );

        foreach (
            [
                'total_received',
                'total_created',
                'total_updated',
                'total_failed',
            ] as $column
        ) {
            $this->addColumn(
                'siakad_sync_logs',
                $column,
                function (
                    Blueprint $table
                ) use ($column): void {
                    $table->unsignedInteger(
                        $column
                    )->default(0);
                }
            );
        }

        $this->addColumn(
            'siakad_sync_logs',
            'message',
            function (Blueprint $table): void {
                $table->text('message')
                    ->nullable();
            }
        );

        $this->addColumn(
            'siakad_sync_logs',
            'payload',
            function (Blueprint $table): void {
                $table->json('payload')
                    ->nullable();
            }
        );

        $this->addUnsignedBigIntegerColumn(
            'siakad_sync_logs',
            'triggered_by'
        );

        $this->addColumn(
            'siakad_sync_logs',
            'started_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'started_at'
                )->nullable();
            }
        );

        $this->addColumn(
            'siakad_sync_logs',
            'finished_at',
            function (Blueprint $table): void {
                $table->timestamp(
                    'finished_at'
                )->nullable();
            }
        );
    }

    private function alignNotifications(): void
    {
        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create(
            'notifications',
            function (Blueprint $table): void {
                $table->uuid('id')
                    ->primary();

                $table->string('type');

                $table->morphs(
                    'notifiable'
                );

                $table->text('data');

                $table->timestamp(
                    'read_at'
                )->nullable();

                $table->timestamps();
            }
        );
    }

    private function backfillLegacyData(): void
    {
        if (
            Schema::hasTable('logbooks')
            && Schema::hasColumn(
                'logbooks',
                'activity'
            )
            && Schema::hasColumn(
                'logbooks',
                'description'
            )
        ) {
            DB::table('logbooks')
                ->whereNull('description')
                ->whereNotNull('activity')
                ->update([
                    'description' =>
                        DB::raw('activity'),
                ]);
        }

        if (
            Schema::hasTable('logbooks')
            && Schema::hasColumn(
                'logbooks',
                'evidence_path'
            )
            && Schema::hasColumn(
                'logbooks',
                'attachment_file'
            )
        ) {
            DB::table('logbooks')
                ->whereNull(
                    'attachment_file'
                )
                ->whereNotNull(
                    'evidence_path'
                )
                ->update([
                    'attachment_file' =>
                        DB::raw(
                            'evidence_path'
                        ),
                ]);
        }

        if (
            Schema::hasTable('logbooks')
            && Schema::hasColumn(
                'logbooks',
                'review_note'
            )
            && Schema::hasColumn(
                'logbooks',
                'validation_notes'
            )
        ) {
            DB::table('logbooks')
                ->whereNull(
                    'validation_notes'
                )
                ->whereNotNull(
                    'review_note'
                )
                ->update([
                    'validation_notes' =>
                        DB::raw(
                            'review_note'
                        ),
                ]);
        }

        if (
            Schema::hasTable(
                'framework_of_references'
            )
            && Schema::hasColumn(
                'framework_of_references',
                'status'
            )
        ) {
            DB::table(
                'framework_of_references'
            )
                ->where(
                    'status',
                    'Disetujui_PL'
                )
                ->update([
                    'status' =>
                        'Menunggu_Review_Dosen',
                ]);
        }
    }

    private function addUnsignedBigIntegerColumn(
        string $table,
        string $column
    ): void {
        $this->addColumn(
            $table,
            $column,
            function (
                Blueprint $blueprint
            ) use ($column): void {
                $blueprint
                    ->unsignedBigInteger(
                        $column
                    )
                    ->nullable();
            }
        );
    }

    private function addColumn(
        string $table,
        string $column,
        callable $definition
    ): void {
        if (
            ! Schema::hasTable($table)
            || Schema::hasColumn(
                $table,
                $column
            )
        ) {
            return;
        }

        Schema::table(
            $table,
            function (
                Blueprint $blueprint
            ) use ($definition): void {
                $definition($blueprint);
            }
        );
    }
};