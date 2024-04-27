<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\FlexibleRender;
use MoonShine\Components\Layout\Box;
use MoonShine\Fields\Date;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Relationships\MorphMany;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\QueryTags\QueryTag;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;

class TestItemResource extends ModelResource
{
    protected string $model = Item::class;

    public string $title = 'Items';

    public array $with = ['category', 'images'];

    public string $column = 'name';

    public function fields(): array
    {
        return [
            Box::make([
                ID::make()->sortable()->useOnImport()->showOnExport(),

                Text::make('Name title', 'name')->sortable(),

                BelongsTo::make('Category title', 'category', 'name', new TestCategoryResource()),

                TinyMce::make('Content title', 'content')
                    ->hideOnIndex(),

                Date::make('Public at title', 'public_at')
                    ->hideOnIndex()
                ,

                HasMany::make('Comments title', 'comments', resource: new TestCommentResource())->fields([
                    ID::make()->sortable(),
                    Text::make('Comment title', 'content')->sortable(),
                    Switcher::make('Active title', 'active')->updateOnPreview(resource: $this),
                ]),

                MorphMany::make('Images title', 'images', resource: new TestImageResource())
                    ->fields([
                        ID::make()->sortable(),
                        Image::make('Image title', 'name'),
                    ]),
            ]),
        ];
    }

    public function filters(): array
    {
        return [
            Text::make('Name'),
            BelongsTo::make('Category', resource: new TestCategoryResource())->nullable(),
        ];
    }

    public function queryTags(): array
    {
        $maxId = Category::query()->max('id');

        return [
            QueryTag::make(
                'Item #1 Query Tag',
                fn ($query) => $query->where('category_id', $maxId) // Query builder
            ),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
