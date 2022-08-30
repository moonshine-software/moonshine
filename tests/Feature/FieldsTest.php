<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature;

use Leeto\MoonShine\Decorations\Decoration;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Resources\MoonShineUserRoleResource;
use Leeto\MoonShine\Tests\TestCase;

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
            $this->testResource()->fieldsCollection()->findFieldByColumn('id')
        );

        $this->assertInstanceOf(
            Text::class,
            $this->testResource()->fieldsCollection()->findFieldByColumn('undefined', Text::make('default'))
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
            $this->testResource()->fieldsCollection()->findFieldByRelation('moonshineUserRole')
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
            $this->testResource()->fieldsCollection()->findFieldByResourceClass(MoonShineUserRoleResource::class)
        );
    }

    /**
     * @test
     * @return void
     */
    public function it_only_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->onlyFields() as $field) {
            $this->assertNotInstanceOf(Decoration::class, $field);
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_table_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->tableFields() as $field) {
            $this->assertIsBool($field->isOnIndex());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_form_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->formFields()->onlyFields() as $field) {
            $this->assertIsBool($field->isOnForm());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_export_fields(): void
    {
        foreach ($this->testResource()->fieldsCollection()->exportFields() as $field) {
            $this->assertIsBool($field->isOnExport());
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_extract_labels(): void
    {
        $labels = $this->testResource()->fieldsCollection()->extractLabels();

        $this->assertArrayHasKey('id', $labels);
        $this->assertArrayHasKey('name', $labels);
    }

    /**
     * @test
     * @return void
     */
    public function it_only_fields_columns(): void
    {
        $columns = $this->testResource()->fieldsCollection()->onlyFieldsColumns();

        $this->assertContains('id', $columns);
        $this->assertContains('name', $columns);
    }

    /**
     * @test
     * @return void
     */
    public function it_request_values(): void
    {
        request()->merge([
            'id' => 1,
            'name' => 'Test',
            'email' => 'test@ya.ru',
        ]);

        $values = $this->testResource()
            ->fieldsCollection()
            ->requestValues();

        $this->assertEquals(1, $values['id']);
        $this->assertEquals('Test', $values['name']);
    }
}
