<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources\WithCustomPages;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\Cover;
use MoonShine\Tests\Fixtures\Pages\CoverResource\CoverPageDetail;
use MoonShine\Tests\Fixtures\Pages\CoverResource\CoverPageForm;
use MoonShine\Tests\Fixtures\Pages\CoverResource\CoverPageIndex;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;

class TestCoverPageResource extends ModelResource
{
    public string $model = Cover::class;

    public string $title = 'Covers';

    public array $with = ['category'];

    public function pages(): array
    {
        return [
            CoverPageIndex::make($this->title()),
            CoverPageForm::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            CoverPageDetail::make(__('moonshine::ui.show')),
        ];
    }

    public function indexFields(): array
    {
        return [
            ID::make('ID'),
            Image::make('Image title', 'image'),
            BelongsTo::make('Category title', 'category', 'name', new TestCategoryResource()),
        ];
    }

    public function detailFields(): array
    {
        return $this->indexFields();
    }

    public function formFields(): array
    {
        return $this->indexFields();
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
