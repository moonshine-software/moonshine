<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;

class TestImageResource extends ModelResource
{
    protected string $model = ImageModel::class;

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            Image::make('', 'name'),
            MorphTo::make('Imageable')->types([
                Item::class => 'name',
                Category::class => 'name',
            ]),
        ];
    }

    public function formFields(): array
    {
        return $this->indexFields();
    }

    public function detailFields(): array
    {
        return $this->indexFields();
    }

    public function exportFields(): array
    {
        return [
            ID::make(),
            MorphTo::make('Imageable')->types([
                Item::class => 'name',
                Category::class => 'name',
            ]),
        ];
    }

    public function importFields(): array
    {
        return $this->exportFields();
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
