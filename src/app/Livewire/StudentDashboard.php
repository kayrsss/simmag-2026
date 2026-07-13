<?php

namespace App\Livewire;

use App\Models\Internship;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDashboard extends Component
{
    public ?Internship $internship = null;

    public int $totalLogbook = 0;

    public int $progress = 0;



    public function mount(): void
    {
        $user = Auth::user();


        if (! $user) {
            return;
        }



        $this->internship = Internship::query()
            ->with([
                'company',
                'period',
                'logbooks',
                'finalReports',
            ])
            ->where('student_id', $user->id)
            ->latest()
            ->first();




        if ($this->internship) {


            $this->totalLogbook =
                $this->internship
                    ->logbooks()
                    ->count();



            $this->progress = match ($this->internship->status) {

                'diajukan' => 25,

                'disetujui' => 50,

                'berjalan' => 75,

                'selesai' => 100,

                default => 0,

            };

        }

    }




    public function render()
    {
        return view('livewire.student-dashboard')
            ->layout('layouts.simmag');
    }
}