<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Form\FormContract;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Fields\Fields;
use MoonShine\Traits\HasDataCast;
use MoonShine\Traits\Makeable;
use Throwable;

/**
 * @method static make(string $action = '', string $method = 'POST', array $fields = [], array $values = [])
 */
final class FormBuilder extends Component implements FormContract, MoonShineRenderable
{
    use Makeable;
    use Macroable;
    use HasDataCast;
    use Conditionable;

    protected $except = [
        'fields',
        'buttons',
        'submitLabel',
    ];

    protected bool $isPrecognitive = false;

    protected bool $isAsync = false;

    protected array $buttons = [];

    protected ?string $submitLabel = null;

    protected ComponentAttributeBag $submitAttributes;

    public function __construct(
        protected string $action = '',
        protected string $method = 'POST',
        protected array $fields = [],
        protected array $values = []
    ) {
        $this->submitAttributes = $this->newAttributeBag([
            'type' => 'submit',
        ]);

        $this->withAttributes([
            'action' => $this->action,
            'method' => $this->method,
            'enctype' => 'multipart/form-data',
            'x-data' => 'formBuilder',
        ]);
    }

    public function customAttributes(array $attributes): static
    {
        $this->attributes = $this->attributes->merge($attributes);

        return $this;
    }

    public function action(string $action): self
    {
        $this->action = $action;

        $this->customAttributes(['action' => $this->action]);

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function precognitive(): self
    {
        $this->isPrecognitive = true;

        return $this;
    }

    public function isPrecognitive(): bool
    {
        return $this->isPrecognitive;
    }

    public function async(): self
    {
        $this->isAsync = true;

        return $this;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }


    public function method(string $method): self
    {
        $this->method = $method;

        $this->customAttributes(['method' => $this->method]);

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function fields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        $fields = Fields::make($this->fields);
        $fields->fillValues(
            $this->getValues(),
            $this->getCastedData()
        );

        return $fields;
    }

    public function fill(array $values = []): self
    {
        $this->values = $values;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getCastedData(): mixed
    {
        return $this->hasCast()
            ? $this->getCast()->hydrate($this->getValues())
            : $this->getValues();
    }

    public function submit(string $label, array $attributes = []): self
    {
        $this->submitLabel = $label;
        $this->submitAttributes->setAttributes(
            $attributes + $this->submitAttributes->getAttributes()
        );

        return $this;
    }

    public function submitLabel(): string
    {
        return $this->submitLabel ?? __('moonshine::ui.save');
    }

    public function buttons(array $buttons = []): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): ActionButtons
    {
        $casted = $this->getCastedData();

        return ActionButtons::make($this->buttons)
            ->onlyVisible($casted)
            ->fillItem($casted);
    }

    /**
     * @throws Throwable
     */
    public function render(): View|Closure|string
    {
        $fields = $this->getFields();
        $xInit = json_encode([
            'whenFields' => array_values($fields->whenFieldsConditions()->toArray()),
        ], JSON_THROW_ON_ERROR);

        $this->customAttributes([
            'x-on:submit.prevent' => $this->isPrecognitive()
                ? 'precognition($event.target)'
                : '$event.target.submit()',
            'x-init' => "init($xInit)",
        ]);

        return view('moonshine::components.form.builder', [
            'attributes' => $this->attributes ?: $this->newAttributeBag(),
            'fields' => $fields,
            'buttons' => $this->getButtons(),
            'submitLabel' => $this->submitLabel(),
            'submitAttributes' => $this->submitAttributes,
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
