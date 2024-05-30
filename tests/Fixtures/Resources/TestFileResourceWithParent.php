<?php

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Traits\Resource\ResourceWithParent;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\ID;

class TestFileResourceWithParent extends TestFileResource
{
    use ResourceWithParent;

    public function indexFields(): array
    {
        $parentId = $this->getParentId();

        return [
            ID::make()->sortable(),
            File::make('File', 'path')->dir('item_files/' . $parentId),
            BelongsTo::make('Item', resource: new TestItemResource()),
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

    protected function getParentResourceClassName(): string
    {
        return TestItemResource::class;
    }

    protected function getParentRelationName(): string
    {
        return 'item';
    }
}
