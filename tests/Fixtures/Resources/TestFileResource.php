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

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            File::make('File', 'path'),
            BelongsTo::make('Item', resource: TestItemResource::class),
        ];
    }

    protected function formFields(): iterable
    {
        return $this->indexFields();
    }

    protected function detailFields(): iterable
    {
        return $this->indexFields();
    }

    protected function rules(mixed $item): array
    {
        return [];
    }
}
