<?php

use Illuminate\Support\Facades\Route;
use Deudev\Authify\Features;
use Deudev\Authify\Http\Controllers\AuthenticatedSessionController;
use Deudev\Authify\Http\Controllers\ConfirmablePasswordController;
use Deudev\Authify\Http\Controllers\ConfirmedPasswordStatusController;
use Deudev\Authify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Deudev\Authify\Http\Controllers\EmailVerificationNotificationController;
use Deudev\Authify\Http\Controllers\EmailVerificationPromptController;
use Deudev\Authify\Http\Controllers\NewPasswordController;
use Deudev\Authify\Http\Controllers\PasswordController;
use Deudev\Authify\Http\Controllers\PasswordResetLinkController;
use Deudev\Authify\Http\Controllers\ProfileInformationController;
use Deudev\Authify\Http\Controllers\RecoveryCodeController;
use Deudev\Authify\Http\Controllers\RegisteredUserController;
use Deudev\Authify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Deudev\Authify\Http\Controllers\TwoFactorAuthenticationController;
use Deudev\Authify\Http\Controllers\TwoFactorQrCodeController;
use Deudev\Authify\Http\Controllers\TwoFactorSecretKeyController;
use Deudev\Authify\Http\Controllers\VerifyEmailController;

Route::group(['middleware' => config('authify.middleware', ['web'])], function () {
    $enableViews = config('authify.views', true);

    // Authentication...
    if ($enableViews) {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:'.config('authify.guard')])
            ->name('login');
    }

    $limiter = config('authify.limiters.login');
    $twoFactorLimiter = config('authify.limiters.two-factor');
    $verificationLimiter = config('authify.limiters.verification', '6,1');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:'.config('authify.guard'),
            $limiter ? 'throttle:'.$limiter : null,
        ]));

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Password Reset...
    if (Features::enabled(Features::resetPasswords())) {
        if ($enableViews) {
            Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
                ->middleware(['guest:'.config('authify.guard')])
                ->name('password.request');

            Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
                ->middleware(['guest:'.config('authify.guard')])
                ->name('password.reset');
        }

        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware(['guest:'.config('authify.guard')])
            ->name('password.email');

        Route::post('/reset-password', [NewPasswordController::class, 'store'])
            ->middleware(['guest:'.config('authify.guard')])
            ->name('password.update');
    }

    // Registration...
    if (Features::enabled(Features::registration())) {
        if ($enableViews) {
            Route::get('/register', [RegisteredUserController::class, 'create'])
                ->middleware(['guest:'.config('authify.guard')])
                ->name('register');
        }

        Route::post('/register', [RegisteredUserController::class, 'store'])
            ->middleware(['guest:'.config('authify.guard')]);
    }

    // Email Verification...
    if (Features::enabled(Features::emailVerification())) {
        if ($enableViews) {
            Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
                ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard')])
                ->name('verification.notice');
        }

        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard'), 'signed', 'throttle:'.$verificationLimiter])
            ->name('verification.verify');

        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard'), 'throttle:'.$verificationLimiter])
            ->name('verification.send');
    }

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
            ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard')])
            ->name('user-profile-information.update');
    }

    // Passwords...
    if (Features::enabled(Features::updatePasswords())) {
        Route::put('/user/password', [PasswordController::class, 'update'])
            ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard')])
            ->name('user-password.update');
    }

    // Password Confirmation...
    if ($enableViews) {
        Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard')]);
    }

    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard')])
        ->name('password.confirmation');

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware([config('authify.auth_middleware', 'auth').':'.config('authify.guard')])
        ->name('password.confirm');

    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest:'.config('authify.guard')])
                ->name('two-factor.login');
        }

        Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                'guest:'.config('authify.guard'),
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]));

        $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [config('authify.auth_middleware', 'auth').':'.config('authify.guard'), 'password.confirm']
            : [config('authify.auth_middleware', 'auth').':'.config('authify.guard')];

        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.enable');

        Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.confirm');

        Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.disable');

        Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.qr-code');

        Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.secret-key');

        Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
            ->middleware($twoFactorMiddleware)
            ->name('two-factor.recovery-codes');

        Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
            ->middleware($twoFactorMiddleware);
    }
});
