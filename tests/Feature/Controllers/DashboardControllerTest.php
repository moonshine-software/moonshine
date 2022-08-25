<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Controllers;

use Leeto\MoonShine\Dashboard\Dashboard;
use Leeto\MoonShine\Dashboard\DashboardBlock;
use Leeto\MoonShine\Metrics\ValueMetric;
use Leeto\MoonShine\Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    public function test_unauthorized()
    {
        $response = $this->getJson(route(config('moonshine.prefix').'.dashboard'));

        $response->assertStatus(401);
    }

    public function test_success()
    {
        app(Dashboard::class)->registerBlocks([
            DashboardBlock::make([
                ValueMetric::make('Test', 1)
            ]),
        ]);

        $response = $this->actingAs($this->user, 'moonshine')->getJson(route(config('moonshine.prefix').'.dashboard'));

        $this->assertArrayHasKey('blocks', $response->json());
    }
}
