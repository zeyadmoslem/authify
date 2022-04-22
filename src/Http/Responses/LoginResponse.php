<?php

namespace Deudev\Authify\Http\Responses;

use Deudev\Authify\Contracts\LoginResponse as LoginResponseContract;
use Deudev\Authify\Authify;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : redirect()->intended(Authify::redirects('login'));
    }
}
