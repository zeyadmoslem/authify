<?php

namespace Deudev\Authify;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Deudev\Authify\Contracts\FailedPasswordConfirmationResponse as FailedPasswordConfirmationResponseContract;
use Deudev\Authify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Deudev\Authify\Contracts\FailedPasswordResetResponse as FailedPasswordResetResponseContract;
use Deudev\Authify\Contracts\FailedTwoFactorLoginResponse as FailedTwoFactorLoginResponseContract;
use Deudev\Authify\Contracts\LockoutResponse as LockoutResponseContract;
use Deudev\Authify\Contracts\LoginResponse as LoginResponseContract;
use Deudev\Authify\Contracts\LogoutResponse as LogoutResponseContract;
use Deudev\Authify\Contracts\PasswordConfirmedResponse as PasswordConfirmedResponseContract;
use Deudev\Authify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Deudev\Authify\Contracts\PasswordUpdateResponse as PasswordUpdateResponseContract;
use Deudev\Authify\Contracts\RegisterResponse as RegisterResponseContract;
use Deudev\Authify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessfulPasswordResetLinkRequestResponseContract;
use Deudev\Authify\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use Deudev\Authify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Deudev\Authify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Deudev\Authify\Http\Responses\FailedPasswordConfirmationResponse;
use Deudev\Authify\Http\Responses\FailedPasswordResetLinkRequestResponse;
use Deudev\Authify\Http\Responses\FailedPasswordResetResponse;
use Deudev\Authify\Http\Responses\FailedTwoFactorLoginResponse;
use Deudev\Authify\Http\Responses\LockoutResponse;
use Deudev\Authify\Http\Responses\LoginResponse;
use Deudev\Authify\Http\Responses\LogoutResponse;
use Deudev\Authify\Http\Responses\PasswordConfirmedResponse;
use Deudev\Authify\Http\Responses\PasswordResetResponse;
use Deudev\Authify\Http\Responses\PasswordUpdateResponse;
use Deudev\Authify\Http\Responses\RegisterResponse;
use Deudev\Authify\Http\Responses\SuccessfulPasswordResetLinkRequestResponse;
use Deudev\Authify\Http\Responses\TwoFactorLoginResponse;
use Deudev\Authify\Http\Responses\VerifyEmailResponse;
use PragmaRX\Google2FA\Google2FA;

class AuthifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/authify.php', 'authify');

        $this->registerResponseBindings();

        $this->app->singleton(TwoFactorAuthenticationProviderContract::class, function ($app) {
            return new TwoFactorAuthenticationProvider(
                $app->make(Google2FA::class),
                $app->make(Repository::class)
            );
        });

        $this->app->bind(StatefulGuard::class, function () {
            return Auth::guard(config('authify.guard', null));
        });
    }

    /**
     * Register the response bindings.
     *
     * @return void
     */
    protected function registerResponseBindings()
    {
        $this->app->singleton(FailedPasswordConfirmationResponseContract::class, FailedPasswordConfirmationResponse::class);
        $this->app->singleton(FailedPasswordResetLinkRequestResponseContract::class, FailedPasswordResetLinkRequestResponse::class);
        $this->app->singleton(FailedPasswordResetResponseContract::class, FailedPasswordResetResponse::class);
        $this->app->singleton(FailedTwoFactorLoginResponseContract::class, FailedTwoFactorLoginResponse::class);
        $this->app->singleton(LockoutResponseContract::class, LockoutResponse::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(TwoFactorLoginResponseContract::class, TwoFactorLoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->singleton(PasswordConfirmedResponseContract::class, PasswordConfirmedResponse::class);
        $this->app->singleton(PasswordResetResponseContract::class, PasswordResetResponse::class);
        $this->app->singleton(PasswordUpdateResponseContract::class, PasswordUpdateResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(SuccessfulPasswordResetLinkRequestResponseContract::class, SuccessfulPasswordResetLinkRequestResponse::class);
        $this->app->singleton(VerifyEmailResponseContract::class, VerifyEmailResponse::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();
        $this->configureRoutes();
    }

    /**
     * Configure the publishable resources offered by the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../stubs/authify.php' => config_path('authify.php'),
            ], 'authify-config');

            $this->publishes([
                __DIR__.'/../stubs/CreateNewUser.php' => app_path('Actions/Authify/CreateNewUser.php'),
                __DIR__.'/../stubs/AuthifyServiceProvider.php' => app_path('Providers/AuthifyServiceProvider.php'),
                __DIR__.'/../stubs/PasswordValidationRules.php' => app_path('Actions/Authify/PasswordValidationRules.php'),
                __DIR__.'/../stubs/ResetUserPassword.php' => app_path('Actions/Authify/ResetUserPassword.php'),
                __DIR__.'/../stubs/UpdateUserProfileInformation.php' => app_path('Actions/Authify/UpdateUserProfileInformation.php'),
                __DIR__.'/../stubs/UpdateUserPassword.php' => app_path('Actions/Authify/UpdateUserPassword.php'),
            ], 'authify-support');

            // $this->publishes([
            //     __DIR__.'/../database/migrations' => database_path('migrations'),
            // ], 'authify-migrations');
        }
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (Authify::$registersRoutes) {
            Route::group([
                'namespace' => 'Deudev\Authify\Http\Controllers',
                'domain' => config('authify.domain', null),
                'prefix' => config('authify.prefix'),
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
            });
        }
    }
}
