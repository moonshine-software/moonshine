<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Tests\Fixtures\Models\FileModel;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;

class TestFileResource extends AbstractTestingResource
{
    protected string $model = FileModel::class;

    protected array $with = ['item'];

    protected function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            File::make('File', 'path'),
            BelongsTo::make('Item', resource: TestItemResource::class),
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

    protected function rules(Model $item): array
    {
        return [];
    }
}
