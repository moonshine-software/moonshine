<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Cover;

class TestCoverResource extends ModelResource
{
    public string $model = Cover::class;

    public string $title = 'Covers';

    public array $with = ['category'];

    public function fields(): array
    {
        return [
            ID::make('ID'),
            Image::make('Image title', 'image'),
            BelongsTo::make('Category title', 'category', 'name', new TestCategoryResource())
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}