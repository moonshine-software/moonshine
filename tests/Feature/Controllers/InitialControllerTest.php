<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Tests\TestCase;

class InitialControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_response(): void
    {
        $response = $this->actingAs($this->adminUser(), 'moonshine')
            ->getJson(route(config('moonshine.prefix').'.initial'));

        $this->assertArrayHasKey('menu', $response->json());
    }
}
