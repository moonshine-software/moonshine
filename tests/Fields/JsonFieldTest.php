<?php

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Database\Factories\MoonshineUserFactory;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Fields\BelongsToMany;
use Leeto\MoonShine\Fields\HasMany;
use Leeto\MoonShine\Fields\HasOne;
use Leeto\MoonShine\Fields\ID;
use Leeto\MoonShine\Fields\Json;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class JsonFieldTest extends TestCase
{
    public function testMakeField()
    {
        $field = Json::make('Names');

        $this->assertEquals('names', $field->field());
        $this->assertEquals('names', $field->name());
        $this->assertEquals('names', $field->id());
        $this->assertNull($field->relation());
        $this->assertEquals('Names', $field->label());
    }

    public function testFields()
    {
        $field = Json::make('Names')->fields([
            Text::make('Name')
        ]);

        $this->assertTrue($field->hasFields());

        foreach ($field->getFields() as $inner) {
            $this->assertInstanceOf(Text::class, $inner);

            $this->assertTrue($inner->hasParent());
            $this->assertEquals($field, $inner->parent());

            $this->assertEquals('name', $inner->field());
            $this->assertEquals('names[${index}][name]', $inner->name());
            $this->assertEquals('names_name', $inner->id());
            $this->assertNull($inner->relation());
            $this->assertEquals('Name', $inner->label());
        }
    }

    public function testRemovable()
    {
        $field = Json::make('Names')->fields([
            Text::make('Name')
        ])->removable();

        $this->assertTrue($field->isRemovable());
    }

    public function testKeyValue()
    {
        $field = Json::make('Names')->keyValue('Key', 'Value');

        $this->assertTrue($field->isKeyValue());

        $this->assertTrue($field->hasFields());

        $keyField = $field->getFields()[0];

        $this->assertInstanceOf(Text::class, $keyField);

        $this->assertTrue($keyField->hasParent());
        $this->assertEquals($field, $keyField->parent());

        $this->assertEquals('key', $keyField->field());
        $this->assertEquals('names[${index}][key]', $keyField->name());
        $this->assertEquals('names_key', $keyField->id());
        $this->assertEquals('Key', $keyField->label());
    }

    public function testSave()
    {
        $user = MoonshineUserFactory::new()->makeOne();

        $field = Json::make('Name')->keyValue();

        $this->assertEquals($user, $field->save($user));
    }
}