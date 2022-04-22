<?php

namespace Deudev\Authify\Http\Responses;

use Illuminate\Http\JsonResponse;
use Deudev\Authify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Deudev\Authify\Authify;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
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
                    ? new JsonResponse('', 204)
                    : redirect()->intended(Authify::redirects('login'));
    }
}
