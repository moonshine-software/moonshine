<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\MorphTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;

class TestImageResource extends ModelResource
{
    protected string $model = ImageModel::class;

    public function fields(): array
    {
        return [
            ID::make()->sortable()->useOnImport()->showOnExport(),
            Image::make('', 'name'),
            MorphTo::make('Imageable')->types([
                Item::class => 'name',
                Category::class => 'name',
            ])->showOnExport()->useOnImport()
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
