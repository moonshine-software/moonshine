<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources\WithCustomPages;

use Illuminate\Database\Eloquent\Model;
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

    public function pages(): array
    {
        return [
            CategoryPageIndex::class,
            CategoryPageForm::class,
            CategoryPageDetail::class,
        ];
    }

    public function indexFields(): array
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

    public function detailFields(): array
    {
        return $this->indexFields();
    }

    public function formFields(): array
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

    public function rules(Model $item): array
    {
        return [];
    }
}
