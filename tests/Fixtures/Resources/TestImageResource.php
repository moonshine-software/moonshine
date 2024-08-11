<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\ImageModel;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;

class TestImageResource extends AbstractTestingResource
{
    protected string $model = ImageModel::class;

    protected function indexFields(): array
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

    protected function formFields(): array
    {
        return $this->indexFields();
    }

    protected function detailFields(): array
    {
        return $this->indexFields();
    }

    protected function exportFields(): array
    {
        return [
            ID::make(),
            MorphTo::make('Imageable')->types([
                Item::class => 'name',
                Category::class => 'name',
            ]),
        ];
    }

    protected function importFields(): array
    {
        return $this->exportFields();
    }

    protected function rules(Model $item): array
    {
        return [];
    }
}
