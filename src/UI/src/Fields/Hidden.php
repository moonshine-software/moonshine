<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Support\Stringify;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Hidden extends Field implements HasDefaultValueContract, CanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.hidden';

    protected string $type = 'hidden';

    protected bool $hasOld = false;

    protected bool $showValue = false;

    protected bool $columnSelection = false;

    public function __construct(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
    {
        parent::__construct($label, $column ?? (\is_string($label) ? $label : null), $formatted);
    }

    public function hasWrapper(): bool
    {
        return false;
    }

    public function showValue(): static
    {
        $this->showValue = true;

        return $this;
    }

    public function isShowValue(): bool
    {
        return $this->showValue;
    }

    protected function viewData(): array
    {
        return [
            'isShowValue' => $this->isShowValue(),
        ];
    }

    protected function prepareViewData(array $data): array
    {
        return [
            'inputAttributes' => $this->getInputAttributes($this->getAttributes(), $data['value'] ?? null),
        ];
    }

    /**
     * Builds the attributes the form.input Blade component produced, so the view renders the input directly.
     */
    private function getInputAttributes(ComponentAttributeBag $attributes, mixed $value): ComponentAttributeBag
    {
        $attributes = $attributes
            ->except(['value', 'isShowValue', 'is-show-value'])
            ->merge(['value' => Stringify::value($value)]);

        $type = $attributes->get('type');

        return $attributes->merge([
            'class' => \in_array($type, ['checkbox', 'radio', 'color', 'range', 'hidden'], true)
                ? 'form-' . Stringify::value($attributes->get('type', 'input'))
                : 'form-input',
            'type' => 'text',
        ]);
    }
}
