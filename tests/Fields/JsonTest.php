<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Database\Factories\MoonshineUserFactory;
use Leeto\MoonShine\Fields\Json;
use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

class JsonTest extends TestCase
{
    public function test_make()
    {
        $field = Json::make('Names');

        $this->assertEquals('names', $field->field());
        $this->assertEquals('names[]', $field->name());
        $this->assertEquals('names', $field->id());
        $this->assertNull($field->relation());
        $this->assertEquals('Names', $field->label());
    }

    public function test_fields()
    {
        $field = Json::make('Names')->fields([
            Text::make('Name')
        ]);

        $this->assertTrue($field->hasFields());

        foreach ($field->getFields() as $inner) {
            $this->assertInstanceOf(Text::class, $inner);

            $this->assertEquals('name', $inner->field());
            $this->assertEquals('names[${index0}][name]', $inner->name());
            //$this->assertEquals('names_name', $inner->id());
            $this->assertNull($inner->relation());
            $this->assertEquals('Name', $inner->label());
        }
    }

    public function test_removable()
    {
        $field = Json::make('Names')->fields([
            Text::make('Name')
        ])->removable();

        $this->assertTrue($field->isRemovable());
    }

    public function test_key_value()
    {
        $field = Json::make('Names')->keyValue('Key', 'Value');

        $this->assertTrue($field->isKeyValue());

        $this->assertTrue($field->hasFields());

        $keyField = $field->getFields()[0];

        $this->assertInstanceOf(Text::class, $keyField);

        $this->assertEquals('key', $keyField->field());
        $this->assertEquals('names[${index0}][key]', $keyField->name());
        //$this->assertEquals('names_key', $keyField->id());
        $this->assertEquals('Key', $keyField->label());
    }

    public function test_save()
    {
        $user = MoonshineUserFactory::new()->makeOne();

        $field = Json::make('Name')->keyValue();

        $this->assertEquals($user, $field->save($user));
    }
}
