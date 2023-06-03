<?php

use MoonShine\Dashboard\Dashboard;
use MoonShine\Dashboard\DashboardBlock;
use MoonShine\Dashboard\ResourcePreview;
use MoonShine\Dashboard\TextBlock;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('controllers');
uses()->group('dashboard');

beforeEach(function (): void {
    $resource = TestResourceBuilder::new(MoonshineUser::class, true);

    app(Dashboard::class)->registerBlocks([
        DashboardBlock::make([
            ValueMetric::make('Value')
                ->value(155),

            TextBlock::make('TextBlock', 'TextBlock text'),

            ResourcePreview::make(
                $resource,
                'ResourcePreview'
            ),

            DonutChartMetric::make('DonutChartMetric')
                ->values(['CutCode' => 10000, 'Apple' => 9999]),

            LineChartMetric::make('LineChartMetric')
                ->line([
                    'Profit' => [
                        now()->format('d.m.Y') => 100,
                    ],
                ]),
        ]),
    ]);
});

it('successful response', function (): void {
    asAdmin()->get(route('moonshine.index'))
        ->assertOk()
        ->assertSee(155)
        ->assertSee('TextBlock')
        ->assertSee('TextBlock text')
        ->assertSee('ResourcePreview')
        ->assertSee('DonutChartMetric')
        ->assertSee('LineChartMetric');
});
