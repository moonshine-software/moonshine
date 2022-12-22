<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\ViewComponents;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Tests\TestCase;
use Leeto\MoonShine\Entities\ModelEntity;
use Leeto\MoonShine\Entities\ModelEntityBuilder;
use Leeto\MoonShine\ViewComponents\Table\Table;
use Leeto\MoonShine\ViewComponents\Table\TableHead;
use Leeto\MoonShine\ViewComponents\Table\TableRow;

class TableTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_properties(): void
    {
        $paginator = $this->testResource()->paginate();

        $paginator->getCollection()->transform(function (Model $values) {
            return (new ModelEntityBuilder($values))->build();
        });

        $table = Table::make(
            $paginator,
            $this->testResource()->fieldsCollection()->tableFields()
        );

        $rows = $table->resolveFieldsPaginator();

        $this->assertInstanceOf(Fields::class, $table->fields());
        $this->assertInstanceOf(LengthAwarePaginator::class, $rows);
        $this->assertInstanceOf(TableHead::class, $table->columns());

        foreach ($rows as $row) {
            $this->assertInstanceOf(TableRow::class, $row);
            $this->assertInstanceOf(Fields::class, $row->fields());
            $this->assertInstanceOf(ModelEntity::class, $row->values());
            $this->assertNotNull($row->id());

            $row->fields()->every(function (Field $field) {
                $this->assertNotNull($field->value());
            });
        }
    }
}
