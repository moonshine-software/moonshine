<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages;

use MoonShine\Crud\QueryTags\QueryTag;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\DateRange;
use MoonShine\UI\Fields\Text;

class TestItemIndexPage extends TestIndexPage
{
    protected function filters(): iterable
    {
        return [
            Box::make([
                Text::make('Name'),
                BelongsTo::make('Category', resource: TestCategoryResource::class)->nullable(),
                DateRange::make('Created at'),
            ]),
        ];
    }

    protected function queryTags(): array
    {
        $maxId = Category::query()->max('id');

        return [
            QueryTag::make(
                'Item #1 Query Tag',
                static fn ($query) => $query->where('category_id', $maxId) // Query builder
            ),
        ];
    }
}
