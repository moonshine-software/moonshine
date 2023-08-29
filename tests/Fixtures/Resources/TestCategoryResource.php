<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Decorations\Block;
use MoonShine\Fields\Date;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\HasOne;
use MoonShine\Fields\Text;
use MoonShine\Fields\TinyMce;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;

class TestCategoryResource extends ModelResource
{
    public string $model = Category::class;

    public string $title = 'Category';

    public array $with = ['cover'];

    public string $column = 'name';

    public function fields(): array
    {
        return [
            Block::make('', [
                ID::make()->sortable(),

                Text::make('Name title', 'name')
                    ->sortable(),

                TinyMce::make('Content title', 'content')
                    ->hideOnIndex(),

                HasOne::make('Cover title', 'cover', resource: new TestCoverResource())->fields([
                    ID::make(),
                    Image::make('HasOne Image title', 'image'),
                ]),

                Date::make('Public at title', 'public_at')
                    ->hideOnIndex()
                    ->showWhen('is_public', 1)
                    ->withTime(),
            ])
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}