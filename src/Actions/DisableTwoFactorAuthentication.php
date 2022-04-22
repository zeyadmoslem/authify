<?php

namespace Deudev\Authify\Actions;

use Deudev\Authify\Events\TwoFactorAuthenticationDisabled;
use Deudev\Authify\Authify;

class DisableTwoFactorAuthentication
{
    /**
     * Disable two factor authentication for the user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function __invoke($user)
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ] + (Authify::confirmsTwoFactorAuthentication() ? [
            'two_factor_confirmed_at' => null,
        ] : []))->save();

        TwoFactorAuthenticationDisabled::dispatch($user);
    }
}
