<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Reset Password - SIMMAG')]
class ResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(
        string $token
    ): void {
        $this->token = $token;

        $this->email = request()
            ->string('email')
            ->toString();
    }

    protected function rules(): array
    {
        return [
            'token' => [
                'required',
                'string',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
                PasswordRule::min(8),
                'confirmed',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'token.required' =>
                'Token reset password tidak tersedia.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'password.required' =>
                'Password baru wajib diisi.',

            'password.min' =>
                'Password baru minimal 8 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sama.',
        ];
    }

    public function resetPassword(): mixed
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' =>
                    trim($this->email),

                'password' =>
                    $this->password,

                'password_confirmation' =>
                    $this->password_confirmation,

                'token' =>
                    $this->token,
            ],
            function (
                User $user,
                string $password
            ): void {
                $user->forceFill([
                    'password' =>
                        Hash::make($password),
                ]);

                $user->setRememberToken(
                    Str::random(60)
                );

                $user->save();

                event(
                    new PasswordResetEvent(
                        $user
                    )
                );
            }
        );

        if (
            $status === Password::PASSWORD_RESET
        ) {
            session()->flash(
                'status',
                'Password berhasil diperbarui. Silakan login menggunakan password baru.'
            );

            return $this->redirectRoute(
                'login',
                navigate: true
            );
        }

        $this->addError(
            'email',
            match ($status) {
                Password::INVALID_TOKEN =>
                    'Tautan reset password tidak valid atau sudah kedaluwarsa.',

                Password::INVALID_USER =>
                    'Email tidak ditemukan.',

                default =>
                    'Password belum dapat diperbarui.',
            }
        );

        return null;
    }

    public function render()
    {
        return view(
            'livewire.auth.reset-password'
        );
    }
}