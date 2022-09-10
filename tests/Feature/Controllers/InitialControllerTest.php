<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;

class InitialControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_response(): void
    {
        $response = $this->authorized()
            ->getJson(MoonShineRouter::to('initial'));

        $this->assertArrayHasKey('menu', $response->json());
    }
}
