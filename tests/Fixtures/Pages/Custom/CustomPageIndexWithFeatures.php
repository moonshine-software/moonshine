<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\Custom;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Crud\QueryTags\QueryTag;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;

class CustomPageIndexWithFeatures extends IndexPage
{
    protected function queryTags(): array
    {
        return [
            QueryTag::make('Item #1 Query Tag', static fn ($query) => $query->where('id', 1)),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons()->add(
            ActionButton::make('Test button'),
        );
    }

    protected function metrics(): array
    {
        return [
            ValueMetric::make('TestValueMetric')->value(fn () => MoonshineUser::query()->count()),
        ];
    }

    /**
     * @param  TableBuilderContract  $component
     *
     * @return TableBuilderContract
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilderContract
    {
        return $component->trAttributes(function (?DataWrapperContract $data, int $row, TableBuilderContract $table) {
            return [
                'data-test-tr-attr' => 'success',
            ];
        })->tdAttributes(function (?DataWrapperContract $data, int $row, int $cell, TableBuilderContract $table) {
            return [
                'data-test-td-attr' => 'success',
            ];
        });
    }
}
