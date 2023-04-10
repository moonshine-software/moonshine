<?php

declare(strict_types=1);

namespace MoonShine\Tests\Dashboard;

use MoonShine\Dashboard\Dashboard;
use MoonShine\Dashboard\DashboardBlock;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_empty_page()
    {
        $response = $this->authorized()
            ->get(route('moonshine.index'));

        $response->assertOk();
        $response->assertViewIs('moonshine::dashboard');
    }

    public function test_has_blocks()
    {
        app(Dashboard::class)->registerBlocks([
            DashboardBlock::make([
                ValueMetric::make('Orders')
                    ->value(100)
                    ->valueFormat('Now {value} orders'),
            ]),
        ]);

        $response = $this->authorized()
            ->get(route('moonshine.index'));

        $response->assertOk();
        $response->assertViewIs('moonshine::dashboard');
        $response->assertViewHas('blocks', app(Dashboard::class)->getBlocks());
        $response->assertSee('Orders');
        $response->assertSee('Now 100 orders');

        $this->assertCount(1, app(Dashboard::class)->getBlocks());
    }
}
