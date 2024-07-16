<?php
declare(strict_types=1);

namespace MoonShine\Tests\UI\Unit;

use Illuminate\Container\Container;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Core\Core;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Fields\Checkbox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $this->field = Checkbox::make('Active');
    }

    #[Test, TestDox('Actual title')]
    public function it_actual_title(): void
    {
        $this->assertEquals('Active', $this->field->getLabel());
    }
}
