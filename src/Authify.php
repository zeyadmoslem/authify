<?php

namespace Deudev\Authify;

use Deudev\Authify\Contracts\ConfirmPasswordViewResponse;
use Deudev\Authify\Contracts\CreatesNewUsers;
use Deudev\Authify\Contracts\LoginViewResponse;
use Deudev\Authify\Contracts\RegisterViewResponse;
use Deudev\Authify\Contracts\RequestPasswordResetLinkViewResponse;
use Deudev\Authify\Contracts\ResetPasswordViewResponse;
use Deudev\Authify\Contracts\ResetsUserPasswords;
use Deudev\Authify\Contracts\TwoFactorChallengeViewResponse;
use Deudev\Authify\Contracts\UpdatesUserPasswords;
use Deudev\Authify\Contracts\UpdatesUserProfileInformation;
use Deudev\Authify\Contracts\VerifyEmailViewResponse;
use Deudev\Authify\Http\Responses\SimpleViewResponse;

class Authify
{
    /**
     * The callback that is responsible for building the authentication pipeline array, if applicable.
     *
     * @var callable|null
     */
    public static $authenticateThroughCallback;

    /**
     * The callback that is responsible for validating authentication credentials, if applicable.
     *
     * @var callable|null
     */
    public static $authenticateUsingCallback;

    /**
     * The callback that is responsible for confirming user passwords.
     *
     * @var callable|null
     */
    public static $confirmPasswordsUsingCallback;

    /**
     * Indicates if Authify routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Get the username used for authentication.
     *
     * @return string
     */
    public static function username()
    {
        return config('authify.username', 'email');
    }

    /**
     * Get the name of the email address request variable / field.
     *
     * @return string
     */
    public static function email()
    {
        return config('authify.email', 'email');
    }

    /**
     * Get a completion redirect path for a specific feature.
     *
     * @param  string  $redirect
     * @return string
     */
    public static function redirects(string $redirect, $default = null)
    {
        return config('authify.redirects.'.$redirect) ?? $default ?? config('authify.home');
    }

    /**
     * Register the views for Authify using conventional names under the given namespace.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function viewNamespace(string $namespace)
    {
        static::viewPrefix($namespace.'::');
    }

    /**
     * Register the views for Authify using conventional names under the given prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public static function viewPrefix(string $prefix)
    {
        static::loginView($prefix.'login');
        static::twoFactorChallengeView($prefix.'two-factor-challenge');
        static::registerView($prefix.'register');
        static::requestPasswordResetLinkView($prefix.'forgot-password');
        static::resetPasswordView($prefix.'reset-password');
        static::verifyEmailView($prefix.'verify-email');
        static::confirmPasswordView($prefix.'confirm-password');
    }

    /**
     * Specify which view should be used as the login view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function loginView($view)
    {
        app()->singleton(LoginViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the two factor authentication challenge view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function twoFactorChallengeView($view)
    {
        app()->singleton(TwoFactorChallengeViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the new password view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function resetPasswordView($view)
    {
        app()->singleton(ResetPasswordViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the registration view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function registerView($view)
    {
        app()->singleton(RegisterViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the email verification prompt.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function verifyEmailView($view)
    {
        app()->singleton(VerifyEmailViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the password confirmation prompt.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function confirmPasswordView($view)
    {
        app()->singleton(ConfirmPasswordViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Specify which view should be used as the request password reset link view.
     *
     * @param  callable|string  $view
     * @return void
     */
    public static function requestPasswordResetLinkView($view)
    {
        app()->singleton(RequestPasswordResetLinkViewResponse::class, function () use ($view) {
            return new SimpleViewResponse($view);
        });
    }

    /**
     * Register a callback that is responsible for building the authentication pipeline array.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function loginThrough(callable $callback)
    {
        static::authenticateThrough($callback);
    }

    /**
     * Register a callback that is responsible for building the authentication pipeline array.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function authenticateThrough(callable $callback)
    {
        static::$authenticateThroughCallback = $callback;
    }

    /**
     * Register a callback that is responsible for validating incoming authentication credentials.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function authenticateUsing(callable $callback)
    {
        static::$authenticateUsingCallback = $callback;
    }

    /**
     * Register a callback that is responsible for confirming existing user passwords as valid.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function confirmPasswordsUsing(callable $callback)
    {
        static::$confirmPasswordsUsingCallback = $callback;
    }

    /**
     * Register a class / callback that should be used to create new users.
     *
     * @param  string  $callback
     * @return void
     */
    public static function createUsersUsing(string $callback)
    {
        app()->singleton(CreatesNewUsers::class, $callback);
    }

    /**
     * Register a class / callback that should be used to update user profile information.
     *
     * @param  string  $callback
     * @return void
     */
    public static function updateUserProfileInformationUsing(string $callback)
    {
        app()->singleton(UpdatesUserProfileInformation::class, $callback);
    }

    /**
     * Register a class / callback that should be used to update user passwords.
     *
     * @param  string  $callback
     * @return void
     */
    public static function updateUserPasswordsUsing(string $callback)
    {
        app()->singleton(UpdatesUserPasswords::class, $callback);
    }

    /**
     * Register a class / callback that should be used to reset user passwords.
     *
     * @param  string  $callback
     * @return void
     */
    public static function resetUserPasswordsUsing(string $callback)
    {
        app()->singleton(ResetsUserPasswords::class, $callback);
    }

    /**
     * Determine if Authify is confirming two factor authentication configurations.
     *
     * @return bool
     */
    public static function confirmsTwoFactorAuthentication()
    {
        return Features::enabled(Features::twoFactorAuthentication()) &&
               Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
    }

    /**
     * Configure Authify to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }
}
