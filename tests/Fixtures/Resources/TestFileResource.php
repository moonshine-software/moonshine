<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\FileModel;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;

class TestFileResource extends ModelResource
{
    protected string $model = FileModel::class;

    protected array $with = ['item'];

    public function indexFields(): array
    {
        return [
            ID::make()->sortable(),
            File::make('File', 'path'),
            BelongsTo::make('Item', resource: TestItemResource::class),
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
