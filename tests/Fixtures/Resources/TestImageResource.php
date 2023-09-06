<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\ImageModel;

class TestImageResource extends ModelResource
{
    protected string $model = ImageModel::class;

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            Image::make('', 'name'),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
