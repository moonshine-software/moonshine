<?php

namespace MoonShine\Tests\Fixtures\Resources;

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\File;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Resources\ModelResource;
use MoonShine\Tests\Fixtures\Models\FileModel;
use MoonShine\Traits\Resource\ResourceWithParent;

class TestFileResourceWithParent extends TestFileResource
{
    use ResourceWithParent;

    public function fields(): array
    {
        $parentId = $this->getParentId();

        return [
            ID::make()->sortable(),
            File::make('File', 'path')->dir('item_files/'.$parentId),
            BelongsTo::make('Item', resource: new TestItemResource()),
        ];
    }

    protected function getParentResourceClassName(): string
    {
        return TestItemResource::class;
    }

    protected function getParentRelationName(): string
    {
        return 'item';
    }
}
