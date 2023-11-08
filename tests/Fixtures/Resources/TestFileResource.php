<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\FileModel;

class TestFileResource extends ModelResource
{
    protected string $model = FileModel::class;

    protected array $with = ['item'];

    public function fields(): array
    {
        return [
            ID::make()->sortable(),
            File::make('File', 'path'),
            BelongsTo::make('Item', resource: new TestItemResource()),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }
}
