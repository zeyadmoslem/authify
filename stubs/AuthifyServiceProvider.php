<?php

namespace App\Providers;

use App\Actions\Authify\CreateNewUser;
use App\Actions\Authify\ResetUserPassword;
use App\Actions\Authify\UpdateUserPassword;
use App\Actions\Authify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Deudev\Authify\Authify;

class AuthifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Authify::createUsersUsing(CreateNewUser::class);
        Authify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Authify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Authify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
