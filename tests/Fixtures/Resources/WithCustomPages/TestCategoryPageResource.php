<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources\WithCustomPages;

use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageDetail;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageForm;
use MoonShine\Tests\Fixtures\Pages\CategoryResource\CategoryPageIndex;
use MoonShine\Tests\Fixtures\Resources\AbstractTestingResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Markdown;
use MoonShine\UI\Fields\Text;

class TestCategoryPageResource extends AbstractTestingResource
{
    public string $model = Category::class;

    public string $title = 'Category';

    public array $with = ['cover'];

    public string $column = 'name';

    protected function pages(): array
    {
        return [
            CategoryPageIndex::class,
            CategoryPageForm::class,
            CategoryPageDetail::class,
        ];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),

            Text::make('Name title', 'name')
                ->sortable(),

            HasOne::make('Cover title', 'cover', resource: TestCoverPageResource::class)->fields([
                ID::make(),
                Image::make('HasOne Image title', 'image'),
            ]),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    protected function formFields(): iterable
    {
        return [
            Box::make([
                ID::make()->sortable(),

                Text::make('Name title', 'name')
                    ->sortable(),

                Markdown::make('Content title', 'content'),

                HasOne::make('Cover title', 'cover', resource: TestCoverPageResource::class)->fields([
                    ID::make(),
                    Image::make('HasOne Image title', 'image'),
                ]),

                Date::make('Public at title', 'public_at')
                    ->showWhen('is_public', 1)
                    ->withTime(),
            ]),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [];
    }
}
