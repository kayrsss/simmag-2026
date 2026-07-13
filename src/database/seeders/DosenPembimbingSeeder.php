<?php

namespace Database\Seeders;

use App\Models\ProgramStudy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class DosenPembimbingSeeder extends Seeder
{
    public function run(): void
    {
        $si = ProgramStudy::query()
            ->where('code', 'SI')
            ->firstOrFail();

        $ti = ProgramStudy::query()
            ->where('code', 'TI')
            ->firstOrFail();

        $role = Role::findOrCreate(
            'dosen_pembimbing',
            'web'
        );

        $lecturers = [
            [
                'nidn' => '0410019001',
                'name' => 'Dr. Andi Pratama, S.Kom., M.Kom.',
                'email' => 'drandipratamaskommkom@lecturer.simmag.test',
                'program_study_id' => $si->id,
            ],
            [
                'nidn' => '0410019002',
                'name' => 'Dr. Budi Hartono, S.Kom., M.Kom.',
                'email' => 'drbudihartonoskommkom@lecturer.simmag.test',
                'program_study_id' => $si->id,
            ],
            [
                'nidn' => '0410019003',
                'name' => 'Dr. Rina Wulandari, S.T., M.Kom.',
                'email' => 'drrinawulandaristmkom@lecturer.simmag.test',
                'program_study_id' => $ti->id,
            ],
            [
                'nidn' => '0410019004',
                'name' => 'Dr. Agus Setiawan, S.T., M.T.',
                'email' => 'dragussetiawanstmt@lecturer.simmag.test',
                'program_study_id' => $ti->id,
            ],
        ];

        DB::transaction(
            function () use (
                $lecturers,
                $role
            ): void {
                foreach ($lecturers as $lecturer) {
                    $user = User::query()
                        ->where(function ($query) use (
                            $lecturer
                        ): void {
                            $query
                                ->where(
                                    'nidn',
                                    $lecturer['nidn']
                                )
                                ->orWhere(
                                    'username',
                                    $lecturer['nidn']
                                )
                                ->orWhere(
                                    'identifier',
                                    $lecturer['nidn']
                                )
                                ->orWhere(
                                    'email',
                                    $lecturer['email']
                                );
                        })
                        ->first();

                    if (! $user) {
                        $user = new User();
                    }

                    $user->forceFill([
                        'name' =>
                            $lecturer['name'],

                        'email' =>
                            $lecturer['email'],

                        'username' =>
                            $lecturer['nidn'],

                        'identifier' =>
                            $lecturer['nidn'],

                        'nidn' =>
                            $lecturer['nidn'],

                        'program_study_id' =>
                            $lecturer['program_study_id'],

                        'institution_name' =>
                            'Universitas Esa Unggul',

                        'role' =>
                            'dosen_pembimbing',

                        'password' =>
                            Hash::make('password'),

                        'email_verified_at' =>
                            now(),

                        'is_active' =>
                            true,
                    ]);

                    $user->save();

                    $user->syncRoles([
                        $role->name,
                    ]);

                    $verifiedUser = $user->fresh();

                    if (
                        ! $verifiedUser
                        || ! Hash::check(
                            'password',
                            (string) $verifiedUser->password
                        )
                    ) {
                        throw new RuntimeException(
                            "Password dosen {$lecturer['nidn']} gagal diverifikasi."
                        );
                    }

                    $this->command?->info(
                        "VERIFIED: {$lecturer['nidn']} / password"
                    );
                }
            }
        );
    }
}