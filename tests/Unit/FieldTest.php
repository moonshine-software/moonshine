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
    public function test_makeable()
    {
        $field = Text::make('Label');

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_field_label()
    {
        $field = Text::make('Name of field');

        $this->assertEquals('Name of field', $field->label());
    }

    public function test_field_column()
    {
        $field = Text::make('Name of field');

        $this->assertEquals('name_of_field', $field->column());

        $field = Text::make('Name of field', 'column');

        $this->assertEquals('column', $field->column());
    }

    public function test_field_relation()
    {
        $field = HasMany::make('Many Relations');

        $this->assertEquals('manyRelations', $field->relation());

        $field = HasMany::make('Many Relations', 'rel');

        $this->assertEquals('rel', $field->relation());
    }

    public function test_field_resource()
    {
        $field = HasMany::make('Many Relations', resource: new RelationResource());

        $this->assertInstanceOf(RelationResource::class, $field->resource());
    }

    public function test_field_resource_column()
    {
        $field = HasMany::make('Many Relations', resource: 'column');

        $this->assertEquals('column', $field->resourceColumn());
    }

    public function test_field_html_attributes()
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

    public function test_field_default_value()
    {
        $field = Text::make('Name of field')->default('Test');

        $this->assertEquals('Test', $field->getDefault());
    }

    public function test_field_show_or_hide_index()
    {
        $field = Text::make('Name of field');

        $this->assertTrue($field->showOnIndex()->isOnIndex());
        $this->assertFalse($field->hideOnIndex()->isOnIndex());
    }

    public function test_field_show_or_hide_form()
    {
        $field = Text::make('Name of field');

        $this->assertTrue($field->showOnForm()->isOnForm());
        $this->assertFalse($field->hideOnForm()->isOnForm());
    }

    public function test_field_show_or_hide_export()
    {
        $field = Text::make('Name of field');

        $this->assertTrue($field->showOnExport()->isOnExport());
        $this->assertFalse($field->hideOnExport()->isOnExport());
    }

    public function test_field_required()
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isRequired());
        $this->assertTrue($field->required()->isRequired());
    }

    public function test_field_sortable()
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isSortable());
        $this->assertTrue($field->sortable()->isSortable());
    }

    public function test_field_nullable()
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isNullable());
        $this->assertTrue($field->nullable()->isNullable());
    }

    public function test_field_readonly()
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isReadonly());
        $this->assertTrue($field->readonly()->isReadonly());
    }

    public function test_field_disabled()
    {
        $field = Text::make('Name of field');

        $this->assertFalse($field->isDisabled());
        $this->assertTrue($field->disabled()->isDisabled());
    }

    public function test_field_component()
    {
        $field = Text::make('Name of field');

        $this->assertEquals('TextField', $field->getComponent());
    }
}
