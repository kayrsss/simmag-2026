<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Lupa Password - SIMMAG')]
class ForgotPassword extends Component
{
    public string $email = '';

    protected function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.max' =>
                'Email maksimal 255 karakter.',
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink([
            'email' => trim($this->email),
        ]);

        if (
            $status === Password::RESET_LINK_SENT
        ) {
            session()->flash(
                'status',
                'Tautan reset password berhasil dikirim. Silakan periksa email Anda.'
            );

            $this->reset('email');

            return;
        }

        $this->addError(
            'email',
            match ($status) {
                Password::INVALID_USER =>
                    'Email tersebut tidak terdaftar di SIMMAG.',

                Password::RESET_THROTTLED =>
                    'Permintaan terlalu cepat. Tunggu beberapa saat lalu coba kembali.',

                default =>
                    'Tautan reset password belum dapat dikirim.',
            }
        );
    }

    public function render()
    {
        return view(
            'livewire.auth.forgot-password'
        );
    }
}