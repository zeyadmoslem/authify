<?php

namespace Deudev\Authify\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Routing\Controller;
use Deudev\Authify\Contracts\VerifyEmailResponse;
use Deudev\Authify\Http\Requests\VerifyEmailRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Deudev\Authify\Http\Requests\VerifyEmailRequest  $request
     * @return \Deudev\Authify\Contracts\VerifyEmailResponse
     */
    public function __invoke(VerifyEmailRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return app(VerifyEmailResponse::class);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return app(VerifyEmailResponse::class);
    }
}
