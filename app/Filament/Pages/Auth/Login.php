<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    public function getFormActions(): array
    {
        return [
            Action::make('factory_login')
                ->label('تسجيل دخول المصنع')
                ->url('/factory/login')
                ->button()
                ->color('info')
                ->extraAttributes(['class' => 'w-full']),
            $this->getAuthenticateFormAction(),
        ];
    }
} 