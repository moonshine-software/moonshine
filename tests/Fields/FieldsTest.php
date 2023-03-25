<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Decorations\Block;
use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Exceptions\FieldsException;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\Fields;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;
use Leeto\MoonShine\Tests\TestCase;
use ReflectionException;

class FieldsTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_find_field_by_column(): void
    {
        $this->assertInstanceOf(
            ID::class,
            $this->testResource()->getFields()->findFieldByColumn('id')
        );

        $this->assertInstanceOf(
            Text::class,
            $this->testResource()->getFields()->findFieldByColumn('undefined', Text::make('default'))
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_find_field_by_relation(): void
    {
        $this->assertInstanceOf(
            BelongsTo::class,
            $this->testResource()->getFields()->findFieldByRelation('moonshineUserRole')
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_find_field_by_resource(): void
    {
        $this->assertInstanceOf(
            BelongsTo::class,
            $this->testResource()->getFields()->findFieldByResourceClass(MoonShineUserRoleResource::class)
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_only_fields(): void
    {
        foreach ($this->testResource()->getFields()->onlyFields() as $field) {
            $this->assertNotInstanceOf(Decoration::class, $field);
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_table_fields(): void
    {
        foreach ($this->testResource()->getFields()->indexFields() as $field) {
            $this->assertIsBool($field->isOnIndex());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_form_fields(): void
    {
        foreach ($this->testResource()->getFields()->formFields()->onlyFields() as $field) {
            $this->assertIsBool($field->isOnForm());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_detail_fields(): void
    {
        foreach ($this->testResource()->getFields()->detailFields()->onlyFields() as $field) {
            $this->assertIsBool($field->isOnDetail());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_export_fields(): void
    {
        foreach ($this->testResource()->getFields()->exportFields() as $field) {
            $this->assertIsBool($field->isOnExport());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_extract_labels(): void
    {
        $labels = $this->testResource()->getFields()->extractLabels();

        $this->assertArrayHasKey('id', $labels);
        $this->assertArrayHasKey('name', $labels);
    }

    /**
     * @test
     * @return void
     */
    public function it_only_fields_columns(): void
    {
        $columns = $this->testResource()->getFields()->onlyFieldsColumns();

        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @throws FieldsException
     */
    public function it_wrap_into_decoration_success(): void
    {
        $fields = Fields::make([
            Text::make('1'),
            Text::make('2'),
        ]);

        $fields = $fields->wrapIntoDecoration(Block::class, 'Label');

        $this->assertInstanceOf(Block::class, $fields->first());
    }

    /**
     * @test
     * @return void
     * @throws ReflectionException
     * @throws FieldsException
     */
    public function it_wrap_into_decoration_throw(): void
    {
        $this->expectException(FieldsException::class);
        $this->expectExceptionMessage(FieldsException::wrapError()->getMessage());

        $fields = Fields::make([
            Text::make('1'),
            Text::make('2'),
        ]);

        $fields->wrapIntoDecoration(Fields::class, 'Label');
    }
}
