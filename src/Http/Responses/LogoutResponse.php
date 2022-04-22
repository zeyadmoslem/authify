<?php

namespace Deudev\Authify\Http\Responses;

use Illuminate\Http\JsonResponse;
use Deudev\Authify\Contracts\LogoutResponse as LogoutResponseContract;
use Deudev\Authify\Authify;

class LogoutResponse implements LogoutResponseContract
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
                    : redirect(Authify::redirects('logout', '/'));
    }
}
