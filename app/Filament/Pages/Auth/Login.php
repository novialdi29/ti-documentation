<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;

class Login extends \Filament\Pages\Auth\Login
{
    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament-panels::components.layout.base';

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->extraAttributes(['class' => 'ti-login-submit']);
    }
}
