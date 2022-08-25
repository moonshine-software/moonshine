<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Laravel\Sanctum\Sanctum;
use Leeto\MoonShine\Tests\TestCase;

class InitialControllerTest extends TestCase
{
    public function test_response()
    {
        Sanctum::actingAs(
            $this->user,
            ['*'],
            'moonshine'
        );

        $response = $this->getJson(route(config('moonshine.prefix').'.initial'));

        $this->assertArrayHasKey('menu', $response->json());
    }
}
