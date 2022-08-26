<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Unit;

use Leeto\MoonShine\Fields\Field;
use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\Fixtures\RelationResource;
use Leeto\MoonShine\Tests\TestCase;

class FieldTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_makeable(): void
    {
        $field = Text::make('Label');

        $this->assertInstanceOf(Field::class, $field);
    }

    /**
     * @test
     * @return void
     */
    public function it_field_label(): void
    {
        $field = Text::make('Name of field');

        $this->assertEquals('Name of field', $field->label());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_column(): void
    {
        $field = Text::make('Name of field');

        $this->assertEquals('name_of_field', $field->column());

        $field = Text::make('Name of field', 'column');

        $this->assertEquals('column', $field->column());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_relation(): void
    {
        $field = HasMany::make('Many Relations');

        $this->assertEquals('manyRelations', $field->relation());

        $field = HasMany::make('Many Relations', 'rel');

        $this->assertEquals('rel', $field->relation());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_resource(): void
    {
        $field = HasMany::make('Many Relations', resource: new RelationResource());

        $this->assertInstanceOf(RelationResource::class, $field->resource());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_resource_column(): void
    {
        $field = HasMany::make('Many Relations', resource: 'column');

        $this->assertEquals('column', $field->resourceColumn());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_html_attributes(): void
    {
        $field = Text::make('Name of field', 'column')
            ->customAttributes([
                'multiple' => true,
                'data-attr' => 'value'
            ]);

        $this->assertEquals('column[]', $field->name());
        $this->assertEquals('column', $field->id());

        $this->assertTrue($field->attributes()->has('data-attr'));
        $this->assertEquals('value', $field->attributes()->get('data-attr'));
    }

    /**
     * @test
     * @return void
     */
    public function it_field_default_value(): void
    {
        $field = Text::make('Name of field')->default('Test');

        $this->assertEquals('Test', $field->getDefault());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_show_or_hide_index(): void
    {
        $field = Text::make('Name of field');

        $this->assertTrue($field->showOnIndex()->isOnIndex());
        $this->assertFalse($field->hideOnIndex()->isOnIndex());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_show_or_hide_form(): void
    {
        $field = Text::make('Name of field');

        $this->assertTrue($field->showOnForm()->isOnForm());
        $this->assertFalse($field->hideOnForm()->isOnForm());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_show_or_hide_export(): void
    {
        $field = Text::make('Name of field');

        $this->assertTrue($field->showOnExport()->isOnExport());
        $this->assertFalse($field->hideOnExport()->isOnExport());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_required(): void
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isRequired());
        $this->assertTrue($field->required()->isRequired());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_sortable(): void
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isSortable());
        $this->assertTrue($field->sortable()->isSortable());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_nullable(): void
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isNullable());
        $this->assertTrue($field->nullable()->isNullable());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_readonly(): void
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isReadonly());
        $this->assertTrue($field->readonly()->isReadonly());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_disabled(): void
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isDisabled());
        $this->assertTrue($field->disabled()->isDisabled());
    }

    /**
     * @test
     * @return void
     */
    public function it_field_component(): void
    {
        $field = Text::make('Name of field');

        $this->assertEquals('TextField', $field->getComponent());
    }
}
