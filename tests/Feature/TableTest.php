<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Resources\MoonShineUserResource;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Table\Table;
use Leeto\MoonShine\Table\TableHead;
use Leeto\MoonShine\Table\TableRow;
use Leeto\MoonShine\Tests\TestCase;

class TableTest extends TestCase
{
    protected Resource $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new MoonShineUserResource();
    }

    public function test_properties()
    {
        $table = Table::make(
            $this->resource,
            $this->resource->paginate(),
            $this->resource->fieldsCollection()->tableFields()
        );

        $rows = $table->resolveFieldsPaginator();

        $this->assertInstanceOf(Fields::class, $table->fields());
        $this->assertInstanceOf(LengthAwarePaginator::class, $rows);
        $this->assertInstanceOf(TableHead::class, $table->columns());

        foreach ($rows as $row) {
            $this->assertInstanceOf(TableRow::class, $row);
            $this->assertInstanceOf(Fields::class, $row->fields());
            $this->assertInstanceOf(Model::class, $row->values());
            $this->assertInstanceOf(Resource::class, $row->resource());
            $this->assertNotNull($row->id());

            $row->fields()->every(function (Field $field) {
                $this->assertNotNull($field->value());
            });
        }
    }
}
