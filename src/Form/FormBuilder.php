<?php

declare(strict_types=1);

namespace MoonShine\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Contracts\Form\FormContract;
use MoonShine\Fields\Fields;
use MoonShine\ItemActions\ItemActions;
use MoonShine\Traits\Makeable;
use Stringable;

/**
 * @method static static make(string $action = '', string $method = 'POST', array $fields = [], array $values = [])
 */
final class FormBuilder extends Component implements FormContract, Stringable
{
    use Makeable;
    use Macroable;
    use Conditionable;

    protected $except = [
        'typeCast',
        'values',
    ];

    protected bool $isPrecognitive = false;

    protected bool $isAsync = false;

    protected array $buttons = [];

    protected ?Closure $rules = null;

    protected ?Closure $messages = null;

    protected ?string $typeCast = null;

    protected ?string $submitLabel = null;

    public function __construct(
        protected string $action = '',
        protected string $method = 'POST',
        protected array $fields = [],
        protected array $values = []
    ) {
    }

    public function action(string $action): self
    {
        $this->action = $action;

        $this->withAttributes(['action' => $this->action]);

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

        $this->withAttributes(['method' => $this->method]);

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

    public function getFields(): Fields
    {
        $fields = Fields::make($this->fields);
        $fields->fillValues($this->getValues(), $this->getCastedValues());

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

    public function cast(string $cast): self
    {
        $this->typeCast = $cast;

        return $this;
    }

    public function getCastedValues(): mixed
    {
        return $this->typeCast
            ? (new $this->typeCast)->forceFill($this->values)
            : $this->values;
    }

    public function submit(string $label): self
    {
        $this->submitLabel = $label;

        return $this;
    }

    public function submitLabel(): string
    {
        return $this->submitLabel ?? __('ui.save');
    }

    public function buttons(array $buttons = []): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): ItemActions
    {
        return ItemActions::make($this->buttons)
            ->onlyVisible($this->getCastedValues());
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.form-builder');
    }

    public function __toString()
    {
        return Blade::renderComponent($this);
    }
}
