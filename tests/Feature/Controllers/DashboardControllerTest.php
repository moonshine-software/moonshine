<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Dashboard\Dashboard;
use Leeto\MoonShine\Dashboard\DashboardBlock;
use Leeto\MoonShine\Metrics\ValueMetric;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_unauthorized(): void
    {
        $response = $this->getJson(MoonShineRouter::to('dashboard'));

        $response->assertUnauthorized();
    }

    /**
     * @test
     * @return void
     */
    public function it_success_response(): void
    {
        Dashboard::blocks([
            DashboardBlock::make([
                ValueMetric::make('Test', 1)
            ]),
        ]);

        $response = $this->authorized()
            ->getJson(MoonShineRouter::to('dashboard'));

        $this->assertArrayHasKey('blocks', $response->json());
    }
}
