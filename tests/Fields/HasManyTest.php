<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\Examples\ResourceHasMany;
use Leeto\MoonShine\Tests\TestCase;

class HasManyTest extends TestCase
{
    public function test_make()
    {
        $field = HasMany::make('Roles');

        $this->assertEquals('roles', $field->field());
        $this->assertEquals('roles[]', $field->name());
        $this->assertEquals('roles', $field->id());
        $this->assertEquals('roles', $field->relation());
        $this->assertEquals('Roles', $field->label());
    }

    public function test_fields()
    {
        $field = HasMany::make('Roles')->fields([
            Text::make('Name')
        ]);

        $this->assertTrue($field->hasFields());

        foreach ($field->getFields() as $inner) {
            $this->assertInstanceOf(Text::class, $inner);

            $this->assertEquals('name', $inner->field());
            $this->assertEquals('roles[${index0}][name]', $inner->name());
            //$this->assertEquals('roles_name', $inner->id());
            $this->assertNull($inner->relation());
            $this->assertEquals('Name', $inner->label());
        }
    }

    public function test_removable()
    {
        $field = HasMany::make('Names')->fields([
            Text::make('Name')
        ])->removable();

        $this->assertTrue($field->isRemovable());
    }

    /*public function test_parents()
    {
        $resource = new ResourceHasMany();

        foreach ($resource->formComponents() as $component) {
            if($component instanceof HasMany) {
                $this->assertFalse($component->hasParent());

                $this->assertEquals('roles[]', $component->name());

                foreach ($component->getFields() as $field) {
                    $this->assertTrue($field->hasParent());
                    $this->assertEquals($component, $field->parent());

                    $this->assertEquals('roles[${index0}][roles2]', $field->name());

                    if($field->hasFields()) {
                        foreach ($field->getFields() as $subField) {
                            $this->assertTrue($subField->hasParent());
                            $this->assertEquals($field, $subField->parent());

                            if($subField->hasFields()) {
                                $this->assertEquals('roles[${index0}][roles2][${index1}][roles3]', $subField->name());

                                foreach ($subField->getFields() as $sField) {
                                    $this->assertTrue($sField->hasParent());
                                    $this->assertEquals($subField, $sField->parent());

                                    $this->assertEquals('roles[${index0}][roles2][${index1}][roles3][${index2}][name]', $sField->name());
                                }
                            } else {
                                $this->assertEquals('roles[${index0}][roles2][${index1}][name]', $subField->name());
                            }
                        }
                    }
                }
            }
        }
    }*/
}
