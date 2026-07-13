<?php

use App\Http\Controllers\Student\ConsultationController;
use App\Http\Middleware\EnsureSimmagRole;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Dashboard\DosenDashboard;
use App\Livewire\Dashboard\MahasiswaDashboard;
use App\Livewire\Dashboard\PembimbingLapanganDashboard;
use App\Livewire\FieldSupervisor\AssessmentIndex as FieldSupervisorAssessmentIndex;
use App\Livewire\FieldSupervisor\FrameworkIndex as FieldSupervisorFrameworkIndex;
use App\Livewire\FieldSupervisor\LogbookIndex as FieldSupervisorLogbookIndex;
use App\Livewire\FieldSupervisor\StudentIndex as FieldSupervisorStudentIndex;
use App\Livewire\Lecturer\AssessmentIndex as LecturerAssessmentIndex;
use App\Livewire\Lecturer\ConsultationIndex as LecturerConsultationIndex;
use App\Livewire\Lecturer\FinalReportIndex as LecturerFinalReportIndex;
use App\Livewire\Lecturer\FrameworkIndex as LecturerFrameworkIndex;
use App\Livewire\Lecturer\LogbookIndex as LecturerLogbookIndex;
use App\Livewire\Lecturer\StudentIndex as LecturerStudentIndex;
use App\Livewire\Shared\AnnouncementIndex;
use App\Livewire\Student\AssessmentResult as StudentAssessmentResult;
use App\Livewire\Student\ConsultationIndex as StudentConsultationIndex;
use App\Livewire\Student\FinalReportIndex as StudentFinalReportIndex;
use App\Livewire\Student\FrameworkIndex as StudentFrameworkIndex;
use App\Livewire\Student\LogbookIndex as StudentLogbookIndex;
use App\Support\SimmagRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()
            ->route('login');
    }

    return redirect()
        ->route('dashboard');
})->name('home');

Route::middleware('guest')
    ->group(function (): void {
        Route::get(
            '/login',
            Login::class
        )->name('login');

        Route::get(
            '/lupa-password',
            ForgotPassword::class
        )->name('password.request');

        Route::get(
            '/reset-password/{token}',
            ResetPassword::class
        )->name('password.reset');
    });

Route::post(
    '/logout',
    function (Request $request) {
        Auth::logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return redirect()
            ->route('login');
    }
)
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    return redirect()
        ->route(
            SimmagRole::dashboardRouteName(
                Auth::user()
            )
        );
})
    ->middleware('auth')
    ->name('dashboard');

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':mahasiswa',
])
    ->group(function (): void {
        Route::get(
            '/dashboard/mahasiswa',
            MahasiswaDashboard::class
        )->name('dashboard.mahasiswa');
    });

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':dosen_pembimbing',
])
    ->group(function (): void {
        Route::get(
            '/dashboard/dosen-pembimbing',
            DosenDashboard::class
        )->name('dashboard.dosen');
    });

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':pembimbing_lapangan',
])
    ->group(function (): void {
        Route::get(
            '/dashboard/pembimbing-lapangan',
            PembimbingLapanganDashboard::class
        )->name(
            'dashboard.pembimbing-lapangan'
        );
    });

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':admin',
])
    ->group(function (): void {
        Route::get(
            '/dashboard/admin',
            AdminDashboard::class
        )->name('dashboard.admin');
    });

Route::middleware('auth')
    ->group(function (): void {
        Route::get(
            '/pengumuman',
            AnnouncementIndex::class
        )->name('announcements.index');
    });

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':mahasiswa',
])
    ->prefix('mahasiswa')
    ->name('student.')
    ->group(function (): void {
        Route::get(
            '/kerangka-acuan',
            StudentFrameworkIndex::class
        )->name('frameworks.index');

        Route::get(
            '/logbook',
            StudentLogbookIndex::class
        )->name('logbooks.index');

        Route::get(
            '/bimbingan',
            StudentConsultationIndex::class
        )->name('consultations.index');

        Route::post(
            '/bimbingan',
            [
                ConsultationController::class,
                'store',
            ]
        )->name('consultations.store');

        Route::delete(
            '/bimbingan/{consultation}',
            [
                ConsultationController::class,
                'destroy',
            ]
        )->name('consultations.destroy');

        Route::get(
            '/laporan-akhir',
            StudentFinalReportIndex::class
        )->name('final-reports.index');

        Route::get(
            '/hasil-penilaian',
            StudentAssessmentResult::class
        )->name('assessments.index');
    });

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':dosen_pembimbing',
])
    ->prefix('dosen-pembimbing')
    ->name('lecturer.')
    ->group(function (): void {
        Route::get(
            '/mahasiswa',
            LecturerStudentIndex::class
        )->name('students.index');

        Route::get(
            '/kerangka-acuan',
            LecturerFrameworkIndex::class
        )->name('frameworks.index');

        Route::get(
            '/monitoring-logbook',
            LecturerLogbookIndex::class
        )->name('logbooks.index');

        Route::get(
            '/bimbingan',
            LecturerConsultationIndex::class
        )->name('consultations.index');

        Route::get(
            '/laporan-akhir',
            LecturerFinalReportIndex::class
        )->name('final-reports.index');

        Route::get(
            '/penilaian-akademik',
            LecturerAssessmentIndex::class
        )->name('assessments.index');
    });

Route::middleware([
    'auth',
    EnsureSimmagRole::class
        . ':pembimbing_lapangan',
])
    ->prefix('pembimbing-lapangan')
    ->name('field-supervisor.')
    ->group(function (): void {
        Route::get(
            '/mahasiswa',
            FieldSupervisorStudentIndex::class
        )->name('students.index');

        Route::get(
            '/kerangka-acuan',
            FieldSupervisorFrameworkIndex::class
        )->name('frameworks.index');

        Route::get(
            '/validasi-logbook',
            FieldSupervisorLogbookIndex::class
        )->name('logbooks.index');

        Route::get(
            '/penilaian-lapangan',
            FieldSupervisorAssessmentIndex::class
        )->name('assessments.index');
    });