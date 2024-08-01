<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Tests\Fixtures\Models\Cover;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;

class TestCoverResource extends AbstractTestingResource
{
    public string $model = Cover::class;

    public string $title = 'Covers';

    public array $with = ['category'];

    public function indexFields(): array
    {
        return [
            ID::make('ID'),
            Image::make('Image title', 'image'),
            BelongsTo::make('Category title', 'category', 'name', TestCategoryResource::class),
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

    public function rules(Model $item): array
    {
        return [];
    }
}
