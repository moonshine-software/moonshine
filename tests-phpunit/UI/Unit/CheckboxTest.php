<?php

declare(strict_types=1);

namespace MoonShine\Tests\UI\Unit;

use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\FormElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(Checkbox::class)]
#[Group('UI')]
#[Group('fields')]
class CheckboxTest extends TestCase
{
    private Checkbox $field;

    protected function setUp(): void
    {
        MoonShineComponent::consoleMode();
        FormElement::requestValueResolver(
            static fn(string|int|null $index, mixed $default, Field $ctx) => $ctx->toValue()
        );

        $this->field = Checkbox::make('Active');
    }

    #[Test, TestDox('Actual column')]
    public function it_actual_column(): void
    {
        $this->assertEquals('active', $this->field->getColumn());
    }

    #[Test, TestDox('Actual label')]
    public function it_actual_label(): void
    {
        $this->assertEquals('Active', $this->field->getLabel());
    }

    #[Test, TestDox('Fill and equal value')]
    public function it_actual_value(): void
    {
        $this->field->fill(false);

        $this->assertFalse($this->field->toValue());
    }

    #[Test, TestDox('Fill and equal value')]
    public function it_apply_successful(): void
    {
        $this->field->fill(true);

        $data = [
            'active' => false
        ];

        $apply = $this->field->apply(function (array $target, bool $value) {
            $target['active'] = $value;

            return $target;
        }, $data);

        $this->assertEquals(['active' => true], $apply);
    }
}
