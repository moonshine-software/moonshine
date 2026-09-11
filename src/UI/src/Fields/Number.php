<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\UI\Components\Rating;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeNumeric;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\InputExtensions\InputNumberUpDown;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\NumberTrait;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;
use TypeError;

class Number extends Field implements HasDefaultValueContract, CanBeNumeric, HasUpdateOnPreviewContract
{
    use NumberTrait;
    use WithInputExtensions;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;

    protected string $view = 'moonshine::fields.input';

    protected string $type = 'number';

    /**
     * @var string[]
     */
    protected array $propertyAttributes = [
        'type',
        'min',
        'max',
        'step',
    ];

    public function buttons(): static
    {
        $this->extension(new InputNumberUpDown());

        return $this;
    }

    protected function resolvePreview(): Renderable|string
    {
        if ($this->isWithStars()) {
            return (string) Rating::make(
                (int) $this->stringifySlotContent(parent::resolvePreview())
            );
        }

        return parent::resolvePreview();
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $value = $this->getRequestValue();

            if ($value === false && ! $this->isNullable()) {
                return $item;
            }

            if ($value === false && $this->isNullable()) {
                data_set($item, $this->getColumn(), null);

                return $item;
            }

            if (! \is_scalar($value) && $value !== null) {
                throw new TypeError('Expected a scalar numeric field value, got ' . get_debug_type($value));
            }

            $value = \is_string($value) ? str_replace(",", ".", $value) : $value;
            $value = str_contains((string) $value, ".") ? (float) $value : (int) $value;

            if (! \is_null($this->max) && $value > $this->max) {
                $value = $this->max;
            }

            if (! \is_null($this->min) && $value < $this->min) {
                $value = $this->min;
            }

            data_set($item, $this->getColumn(), $value);

            return $item;
        };
    }

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
