<?php

namespace Deudev\Authify\Tests;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Deudev\Authify\Authify;

class FortifyServiceProviderTest extends OrchestraTestCase
{
    public function test_views_can_be_customized()
    {
        Authify::loginView(function () {
            return 'foo';
        });

        $response = $this->get('/login');

        $response->assertOk();
        $this->assertSame('foo', $response->content());
    }

    public function test_customized_views_can_return_their_own_responsable()
    {
        Authify::loginView(function () {
            return new class implements Responsable
            {
                public function toResponse($request)
                {
                    return new JsonResponse(['foo' => 'bar']);
                }
            };
        });

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertExactJson(['foo' => 'bar']);
    }
}
