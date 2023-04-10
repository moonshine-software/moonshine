<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fields;

use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Tests\TestCase;

class SlugFieldTest extends TestCase
{
    public function test_make(): void
    {
        $field = Text::make('First name', 'name');

        $this->assertEquals('text', $field->type());

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
