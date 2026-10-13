<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username / Email')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['username'];

        // Cek apakah input cocok dengan email atau username
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if ($user) {
            $loginType = ($user->email === $login) ? 'email' : 'username';

            return [
                $loginType => $login,
                'password' => $data['password'],
            ];
        }

        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $loginType => $login,
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }
}

