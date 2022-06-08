<?php

namespace Leeto\MoonShine\Tests\Fields;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Leeto\MoonShine\Database\Factories\MoonshineUserFactory;
use Leeto\MoonShine\Fields\BelongsTo;
use Leeto\MoonShine\Tests\TestCase;

class BelongsToFieldTest extends TestCase
{
    use RefreshDatabase;

    public function testMakeField()
    {
        $field = BelongsTo::make('Role', 'admin_role_id');

        $this->assertEquals('admin_role_id', $field->field());
        $this->assertEquals('admin_role_id', $field->name());
        $this->assertEquals('admin_role_id', $field->id());
        $this->assertEquals('adminRole', $field->relation());
        $this->assertEquals('Role', $field->label());
    }

    public function testCallback()
    {
        Artisan::call('migrate', ['--path' => 'database/migrations']);

        $user = MoonshineUserFactory::new()->makeOne();

        $field = BelongsTo::make('Role', 'moonshine_user_role_id', fn($item) => "$item->id.) $item->name");

        $this->assertEquals([1 => '1.) Admin'], $field->relatedOptions($user));
    }
}