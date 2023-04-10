<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fields;

use Illuminate\Foundation\Testing\RefreshDatabase;
use MoonShine\Database\Factories\MoonshineUserFactory;
use MoonShine\Fields\BelongsTo;
use MoonShine\Tests\TestCase;

class BelongsToTest extends TestCase
{
    use RefreshDatabase;

    public function test_make()
    {
        $field = BelongsTo::make('Role', 'admin_role_id');

        $this->assertEquals('admin_role_id', $field->field());
        $this->assertEquals('admin_role_id', $field->name());
        $this->assertEquals('admin_role_id', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Role', $field->label());
        $this->assertEquals('', $field->type());
    }

    public function test_callback()
    {
        $user = MoonshineUserFactory::new()->makeOne();

        $field = BelongsTo::make('Role', 'moonshine_user_role_id', fn ($item) => "$item->id.) $item->name");

        $this->assertEquals([1 => '1.) Admin'], $field->relatedValues($user));
    }
}
