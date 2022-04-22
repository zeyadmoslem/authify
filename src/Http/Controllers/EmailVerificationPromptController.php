<?php

namespace Deudev\Authify\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Deudev\Authify\Contracts\VerifyEmailViewResponse;
use Deudev\Authify\Authify;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Deudev\Authify\Contracts\VerifyEmailViewResponse
     */
    public function __invoke(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(Authify::redirects('email-verification'))
                    : app(VerifyEmailViewResponse::class);
    }
}
