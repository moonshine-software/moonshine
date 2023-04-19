<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fields;

use MoonShine\Decorations\Block;
use MoonShine\Decorations\Decoration;
use MoonShine\Exceptions\FieldsException;
use MoonShine\Fields\BelongsTo;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
use MoonShine\Fields\Text;
use MoonShine\Resources\MoonShineUserRoleResource;
use MoonShine\Tests\Fixtures\TestResource\TestResourceBuilder;
use MoonShine\Tests\TestCase;
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
     * @throws \Throwable
     */
    public function it_can_see_field()
    {
        $testResource = TestResourceBuilder::buildForCanSeeTest();

        $this->assertNotEmpty($testResource->getFields());

        foreach ($testResource->getFields()->indexFields() as $field) {
            match ($field->field()) {
                'id', 'email' => $this->assertTrue($field->isSee($this->adminUser)),
                'name' => $this->assertFalse($field->isSee($this->adminUser)),
            };
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
