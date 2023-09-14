<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Email;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Password;
use MoonShine\Fields\Text;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShine;
use MoonShine\QueryTags\QueryTag;

class TestResourceBuilder
{
    public static function new(string $model = null): TestResource
    {
        $resource = new TestResource();

        if ($model) {
            $resource->setTestModel($model);
        }

        MoonShine::addResource($resource);

        return $resource;
    }

    public static function testResourceWithAllFeatures(): TestResource
    {
        return TestResourceBuilder::new(
            MoonShineUser::class
        )
            ->setTestFields([
                Text::make('Name'),
                Email::make('Email'),
                Password::make('Password'),
                NoInput::make('Badge')->badge(fn () => 'red'),
            ])
            ->setTestQueryTags([
                QueryTag::make(
                    'Item #1 Query Tag',
                    fn ($query) => $query->where('id', 1) // Query builder
                ),
            ])
            ->setTestButtons([
                ActionButton::make(
                    'Test button',
                    url: fn (): string => '/'
                )->showInLine(),
            ])
            ->setTestMetrics([
                ValueMetric::make('TestValueMetric')->value(MoonshineUser::query()->count()),
                LineChartMetric::make('TestLineChartMetric')->line(['Line' => [ 1 => 100, 2 => 200, 3 => 300]]),
                DonutChartMetric::make('TestDonutChartMetric')->values(['CutCode' => 10000, 'Apple' => 9999]),
            ])
            ->setTestTdAttributes(function (
                mixed $data,
                int $row,
                int $cell,
                ComponentAttributeBag $attr
            ): ComponentAttributeBag {
                $attr->setAttributes([
                    'data-test-td-attr' => 'success',
                ]);

                return $attr;
            })->setTestTrAttributes(function (
                mixed $data,
                int $row,
                ComponentAttributeBag $attr
            ): ComponentAttributeBag {
                if($row == 1) {
                    $attr->setAttributes([
                        'data-test-tr-attr' => 'success',
                    ]);
                }

                return $attr;
            })
        ;
    }
}
