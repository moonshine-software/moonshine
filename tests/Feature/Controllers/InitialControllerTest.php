<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Tests\TestCase;

class InitialControllerTest extends TestCase
{
    public function test_response()
    {
        $response = $this->actingAs($this->user, 'moonshine')->getJson(route(config('moonshine.prefix').'.initial'));

        $this->assertArrayHasKey('menu', $response->json());
    }
}
