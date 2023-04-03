<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Fields;

use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Fields\Slug;
use Leeto\MoonShine\Tests\TestCase;

class SlagFieldTest extends TestCase
{
    public function test_make(): void
    {
        $field = Text::make('First name', 'name');

        $slug_1 = Slug::make('SLUG', 'slug')->from('name');

        $this->assertEquals($slug_1->getFrom(), $field->name());
        $this->assertEquals($slug_1->getSeparator(), '-');
        $this->assertEquals($slug_1->isUnique(), false);

        $slug_2 = Slug::make('SLUG', 'slug')->from('name')->separator('_')->unique();

        $this->assertEquals($slug_2->getFrom(), $field->field());
        $this->assertEquals($slug_2->getSeparator(), '_');
        $this->assertEquals($slug_2->isUnique(), true);
    }
}
