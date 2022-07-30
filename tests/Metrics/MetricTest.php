<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Metrics;

use Leeto\MoonShine\Metrics\LineChartMetric;
use Leeto\MoonShine\Metrics\ValueMetric;
use Leeto\MoonShine\Tests\TestCase;

class MetricTest extends TestCase
{
    public function test_value_metric()
    {
        $metric = ValueMetric::make('Orders')->value(100);

        $this->assertEquals('Orders', $metric->label());
        $this->assertEquals(100, $metric->valueResult());
        $this->assertFalse($metric->isProgress());

        $metric = ValueMetric::make('Orders')
            ->value(100)
            ->progress(200);

        $this->assertTrue($metric->isProgress());
        $this->assertEquals(200, $metric->target);

        $metric = ValueMetric::make('Orders')
            ->value(100)
            ->valueFormat('Prefix {value} suffix');

        $this->assertEquals('Prefix 100 suffix', $metric->valueResult());
    }

    public function test_line_chart_metric()
    {
        $metric = LineChartMetric::make('Total')
            ->line(['Line 1' => ['2020' => 100, '2021' => 200, '2022' => 300]], '#fff')
            ->line(['Line 2' => ['2023' => 0, '2019' => 100, '2020' => 200, '2021' => 300, '2022' => 400]], '#000');


        $this->assertEquals('Total', $metric->label());
        $this->assertCount(2, $metric->lines());
        $this->assertEquals(['2019', '2020', '2021', '2022', '2023'], $metric->labels());
    }
}
