<?php

namespace Deudev\Authify\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Deudev\Authify\Actions\ConfirmTwoFactorAuthentication;

class ConfirmedTwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Deudev\Authify\Actions\ConfirmTwoFactorAuthentication  $confirm
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request, ConfirmTwoFactorAuthentication $confirm)
    {
        $confirm($request->user(), $request->input('code'));

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('status', 'two-factor-authentication-confirmed');
    }
}
