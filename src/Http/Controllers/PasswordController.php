<?php

namespace Deudev\Authify\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Deudev\Authify\Contracts\PasswordUpdateResponse;
use Deudev\Authify\Contracts\UpdatesUserPasswords;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Deudev\Authify\Contracts\UpdatesUserPasswords  $updater
     * @return \Deudev\Authify\Contracts\PasswordUpdateResponse
     */
    public function update(Request $request, UpdatesUserPasswords $updater)
    {
        $updater->update($request->user(), $request->all());

        return app(PasswordUpdateResponse::class);
    }
}
