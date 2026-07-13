<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan relasi akun Pembimbing Lapangan
     * ke data magang.
     */
    public function up(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->foreignId('field_supervisor_id')
                ->nullable()
                ->after('supervisor_lecturer_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Menghapus relasi Pembimbing Lapangan.
     */
    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'field_supervisor_id'
            );
        });
    }
};