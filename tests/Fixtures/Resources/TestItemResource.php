<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\MorphMany;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Markdown;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Text;

class TestItemResource extends ModelResource
{
    protected string $model = Item::class;

    public string $title = 'Items';

    public array $with = ['category', 'images'];

    public string $column = 'name';

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name title', 'name')->sortable(),

            BelongsTo::make('Category title', 'category', 'name', TestCategoryResource::class),

            HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)->fields([
                ID::make()->sortable(),
                Text::make('Comment title', 'content')->sortable(),
                Switcher::make('Active title', 'active')->updateOnPreview(resource: $this),
            ]),

            MorphMany::make('Images title', 'images', resource: TestImageResource::class)
                ->fields([
                    ID::make()->sortable(),
                    Image::make('Image title', 'name'),
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

                Text::make('Name title', 'name')->sortable(),

                BelongsTo::make('Category title', 'category', 'name', TestCategoryResource::class),

                Markdown::make('Content title', 'content'),

                Date::make('Public at title', 'public_at'),

                HasMany::make('Comments title', 'comments', resource: TestCommentResource::class)->fields([
                    ID::make()->sortable(),
                    Text::make('Comment title', 'content')->sortable(),
                    Switcher::make('Active title', 'active')->updateOnPreview(resource: $this),
                ]),

                MorphMany::make('Images title', 'images', resource: TestImageResource::class)
                    ->fields([
                        ID::make()->sortable(),
                        Image::make('Image title', 'name'),
                    ]),
            ]),
        ];
    }

    public function exportFields(): array
    {
        return [
            ID::make(),
        ];
    }

    public function importFields(): array
    {
        return $this->exportFields();
    }

    public function filters(): array
    {
        return [
            Text::make('Name'),
            BelongsTo::make('Category', resource: TestCategoryResource::class)->nullable(),
        ];
    }

    public function queryTags(): array
    {
        $maxId = Category::query()->max('id');

        return [
            QueryTag::make(
                'Item #1 Query Tag',
                static fn ($query) => $query->where('category_id', $maxId) // Query builder
            ),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
