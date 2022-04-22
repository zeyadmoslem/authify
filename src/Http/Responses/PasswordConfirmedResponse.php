<?php

namespace Deudev\Authify\Http\Responses;

use Illuminate\Http\JsonResponse;
use Deudev\Authify\Contracts\PasswordConfirmedResponse as PasswordConfirmedResponseContract;
use Deudev\Authify\Authify;

class PasswordConfirmedResponse implements PasswordConfirmedResponseContract
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
                    ? new JsonResponse('', 201)
                    : redirect()->intended(Authify::redirects('password-confirmation'));
    }
}
