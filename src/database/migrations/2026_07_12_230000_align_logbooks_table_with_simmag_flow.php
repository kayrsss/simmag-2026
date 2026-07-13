<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('logbooks')) {
            return;
        }

        if (! Schema::hasColumn('logbooks', 'internship_id')) {
            Schema::table('logbooks', function (Blueprint $table): void {
                $table
                    ->foreignId('internship_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('internships')
                    ->cascadeOnDelete();
            });
        }

        if (
            ! Schema::hasColumn(
                'logbooks',
                'framework_of_reference_id'
            )
        ) {
            Schema::table('logbooks', function (Blueprint $table): void {
                $table
                    ->foreignId('framework_of_reference_id')
                    ->nullable()
                    ->after('internship_id')
                    ->constrained('framework_of_references')
                    ->nullOnDelete();
            });
        }

        if (
            ! Schema::hasIndex(
                'logbooks',
                'logbooks_internship_activity_date_index'
            )
        ) {
            Schema::table('logbooks', function (Blueprint $table): void {
                $table->index(
                    [
                        'internship_id',
                        'activity_date',
                    ],
                    'logbooks_internship_activity_date_index'
                );
            });
        }
    }

    public function down(): void
    {
        /*
         * Migration ini hanya menjaga kompatibilitas database lama.
         *
         * Kolom dan index utama sudah tersedia pada migration:
         * 2026_07_04_211001_create_logbooks_table.php
         *
         * Karena itu rollback migration ini tidak menghapus
         * struktur utama tabel logbooks.
         */
    }
};