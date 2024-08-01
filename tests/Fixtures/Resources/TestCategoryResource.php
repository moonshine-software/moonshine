<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Markdown;
use MoonShine\UI\Fields\Text;

class TestCategoryResource extends AbstractTestingResource
{
    public string $model = Category::class;

    public string $title = 'Category';

    public array $with = ['cover'];

    public string $column = 'name';

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name title', 'name')->sortable(),

            Markdown::make('Content title', 'content'),

            HasOne::make('Cover title', 'cover', resource: TestCoverResource::class)->fields([
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
                ID::make(),

                Text::make('Name title', 'name'),

                Markdown::make('Content title', 'content'),

                HasOne::make('Cover title', 'cover', resource: TestCoverResource::class)->fields([
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
