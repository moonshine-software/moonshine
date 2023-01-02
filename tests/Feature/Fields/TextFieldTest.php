<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Tests\Feature\Fields;

use Leeto\MoonShine\Fields\Text;
use Leeto\MoonShine\Tests\TestCase;

final class TextFieldTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function it_mask(): void
    {
        $field = Text::make('Name')
            ->mask('999-999');

        $this->assertEquals('999-999', $field->getMask());
    }

    /**
     * @test
     * @return void
     */
    public function it_callback_value(): void
    {
        $field = Text::make('Name', 'name', function ($value) {
            return $value['last_name'].' '.$value['first_name'];
        })->setValue([
            'first_name' => 'First',
            'last_name' => 'Last'
        ]);

        $this->assertEquals('Last First', $field->formattedValue());
    }
}
