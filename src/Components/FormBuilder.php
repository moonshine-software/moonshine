<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Traits\Fields\WithAdditionalFields;
use MoonShine\Traits\HasAsync;

/**
 * @method static static make(string $action = '', string $method = 'POST', Fields|array $fields = [], mixed $values = [])
 */
final class FormBuilder extends RowComponent
{
    use HasAsync;

    use WithAdditionalFields;

    protected string $view = 'moonshine::components.form.builder';

    protected $except = [
        'fields',
        'buttons',
        'submitLabel',
    ];

    protected bool $isPrecognitive = false;

    protected ?string $submitLabel = null;

    protected ComponentAttributeBag $submitAttributes;

    protected ?Closure $onBeforeFieldsRender = null;

    public function __construct(
        protected string $action = '',
        protected string $method = 'POST',
        Fields|array $fields = [],
        mixed $values = []
    ) {
        $this->fields($fields);
        $this->fill($values);

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

    public function action(string $action): self
    {
        $this->action = $action;
        $this->attributes['action'] = $this->action;

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

    protected function prepareAsyncUrl(?string $asyncUrl = null): ?string
    {
        return $asyncUrl ?? $this->getAction();
    }

    public function method(string $method): self
    {
        $this->method = $method;
        $this->attributes['method'] = $this->method;

        return $this;
    }

    public function redirect(?string $uri = null): self
    {
        if (! is_null($uri)) {
            $this->additionalFields[] = Hidden::make('_redirect')
                ->setValue($uri);
        }

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function submit(string $label, array $attributes = []): self
    {
        $this->submitLabel = $label;
        $this->submitAttributes->setAttributes(
            $attributes + $this->submitAttributes->getAttributes()
        );

        return $this;
    }

    public function submitAttributes(): ComponentAttributeBag
    {
        return $this->submitAttributes;
    }

    public function submitLabel(): string
    {
        return $this->submitLabel ?? __('moonshine::ui.save');
    }

    public function switchFormMode(bool $isAsync, string|array|null $asyncEvents = ''): self
    {
        return $isAsync ? $this->async(asyncEvents: $asyncEvents) : $this->precognitive();
    }

    public function onBeforeFieldsRender(Closure $closure): self
    {
        $this->onBeforeFieldsRender = $closure;

        return $this;
    }

    protected function viewData(): array
    {
        $fields = $this->preparedFields();

        if($this->hasAdditionalFields()) {
            $this->getAdditionalFields()->each(fn($field) => $fields->push($field));
        }

        if (! is_null($this->getName())) {
            $fields->onlyFields()->each(
                fn (Field $field): Field => $field->formName($this->getName())
            );

            $fields->prepend(
                Hidden::make('_component_name')->setValue($this->getName())
            );
        }

        $xInit = json_encode([
            'whenFields' => array_values($fields->whenFieldsConditions()->toArray()),
        ], JSON_THROW_ON_ERROR);

        $this->customAttributes([
            'x-init' => "init($xInit)",
        ]);

        if ($this->isPrecognitive()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'precognition($event.target)',
            ]);
        }

        if ($this->isAsync()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'async($event.target, `' . $this->asyncEvents() . '`)',
                '@form-reset-' . ($this->getName() ?? 'default') . '.window' => 'formReset',
            ]);
        }

        if(! is_null($this->onBeforeFieldsRender)) {
            $fields = value($this->onBeforeFieldsRender, $fields);
        }

        return [
            'fields' => $fields,
            'precognitive' => $this->isPrecognitive(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->asyncUrl(),
            'buttons' => $this->getButtons(),
            'submitLabel' => $this->submitLabel(),
            'submitAttributes' => $this->submitAttributes(),
        ];
    }
}
