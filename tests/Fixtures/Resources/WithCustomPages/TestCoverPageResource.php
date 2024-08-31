<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources\WithCustomPages;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Tests\Fixtures\Models\Cover;
use MoonShine\Tests\Fixtures\Pages\CoverResource\CoverPageDetail;
use MoonShine\Tests\Fixtures\Pages\CoverResource\CoverPageForm;
use MoonShine\Tests\Fixtures\Pages\CoverResource\CoverPageIndex;
use MoonShine\Tests\Fixtures\Resources\AbstractTestingResource;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;

class TestCoverPageResource extends AbstractTestingResource
{
    public string $model = Cover::class;

    public string $title = 'Covers';

    public array $with = ['category'];

    protected function pages(): array
    {
        return [
            CoverPageIndex::class,
            CoverPageForm::class,
            CoverPageDetail::class,
        ];
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make('ID'),
            Image::make('Image title', 'image'),
            BelongsTo::make('Category title', 'category', 'name', TestCategoryResource::class),
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    protected function formFields(): iterable
    {
        return $this->indexFields();
    }

    protected function rules(mixed $item): array
    {
        return [];
    }
}
