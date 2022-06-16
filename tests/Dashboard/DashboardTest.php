<?php

namespace Leeto\MoonShine\Tests\Dashboard;

use Leeto\MoonShine\Dashboard\Dashboard;
use Leeto\MoonShine\Dashboard\DashboardBlock;
use Leeto\MoonShine\Metrics\ValueMetric;
use Leeto\MoonShine\Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_empty_page()
    {
        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
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
            ])
        ]);

        $response = $this->actingAs($this->user, config('moonshine.auth.guard'))
            ->get(route('moonshine.index'));

        $response->assertOk();
        $response->assertViewIs('moonshine::dashboard');
        $response->assertViewHas('blocks', app(Dashboard::class)->getBlocks());
        $response->assertSee('Orders');
        $response->assertSee('Now 100 orders');

        $this->assertCount(1, app(Dashboard::class)->getBlocks());

    }
}