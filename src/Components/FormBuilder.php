<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use JsonException;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Page;
use MoonShine\Support\AlpineJs;
use MoonShine\Traits\Fields\WithAdditionalFields;
use MoonShine\Traits\HasAsync;
use Throwable;

/**
 * @method static static make(string $action = '', string $method = 'POST', Fields|array $fields = [], mixed $values = [])
 */
final class FormBuilder extends RowComponent
{
    use HasAsync;

    use WithAdditionalFields;

    protected string $view = 'moonshine::components.form.builder';

    protected array $excludeFields = [
        '_force_redirect',
        '_redirect',
        '_method',
        '_component_name',
        '_async_field',
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

    /**
     * @throws Throwable
     */
    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $events = [],
        ?string $callback = null,
        ?Page $page = null,
        ?ResourceContract $resource = null,
    ): self {
        $asyncUrl = moonshineRouter()->asyncMethod(
            $method,
            $message,
            params: ['resourceItem' => $resource?->getItemID()],
            page: $page,
            resource: $resource
        );

        return $this->action($asyncUrl)->async(
            $asyncUrl,
            asyncEvents: $events,
            asyncCallback: $callback
        );
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

    public function getExcludedFields(): array
    {
        return $this->excludeFields;
    }

    public function excludeFields(array $excludeFields): self
    {
        $this->excludeFields = array_merge($this->excludeFields, $excludeFields);

        return $this;
    }

    public function onBeforeFieldsRender(Closure $closure): self
    {
        $this->onBeforeFieldsRender = $closure;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function apply(
        Closure $apply,
        ?Closure $default = null,
        ?Closure $before = null,
        ?Closure $after = null,
        bool $throw = false,
    ): bool {
        $values = $this->getValues();

        if (is_null($default)) {
            $default = static fn (Field $field): Closure => static function (mixed $item) use ($field): mixed {
                if (! $field->hasRequestValue() && ! $field->defaultIfExists()) {
                    return $item;
                }

                $value = $field->requestValue() !== false ? $field->requestValue() : null;

                data_set($item, $field->column(), $value);

                return $item;
            };
        }

        try {
            $fields = $this
                ->preparedFields()
                ->exceptElements(
                    fn (Field $element): bool => in_array($element->column(), $this->getExcludedFields(), true)
                );

            $values = is_null($before) ? $values : $before($values);

            $fields->each(fn (Field $field): mixed => $field->beforeApply($values));

            $fields
                ->withoutOutside()
                ->each(fn (Field $field): mixed => $field->apply($default($field), $values));

            $apply($values, $fields);

            $fields->each(fn (Field $field): mixed => $field->afterApply($values));

            value($after, $values);
        } catch (Throwable $e) {
            report_if(! $throw, $e);
            throw_if($throw, $e);

            return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     * @throws JsonException
     */
    protected function viewData(): array
    {
        $fields = $this->preparedFields();

        if ($this->hasAdditionalFields()) {
            $this->getAdditionalFields()->each(fn ($field) => $fields->push($field));
        }

        $onlyFields = $fields->onlyFields();

        if (! is_null($this->getName())) {
            $onlyFields->each(
                fn (Field $field): Field => $field->formName($this->getName())
            );

            $fields->prepend(
                Hidden::make('_component_name')->setValue($this->getName())
            );
        }

        $reactiveFields = $onlyFields->reactiveFields()
            ->mapWithKeys(fn(Field $field): array => [$field->column() => $field->value()]);

        $xInit = json_encode([
            'whenFields' => array_values($onlyFields->whenFieldsConditions()->toArray()),
            'reactiveUrl' => $reactiveFields->isNotEmpty() ? moonshineRouter()->reactive() : ''
        ], JSON_THROW_ON_ERROR);

        $this->customAttributes([
            'x-data' => "formBuilder(`{$this->getName()}`, {$reactiveFields->toJson()})",
            'x-init' => "init($xInit)",
        ]);

        if ($this->isPrecognitive()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'precognition()',
            ]);
        }

        if ($this->isAsync()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'async(`' . $this->asyncEvents(
                ) . '`, `' . $this->asyncCallback() . '`)',
                AlpineJs::eventBlade(JsEvent::FORM_RESET, $this->getName()) => 'formReset',
            ]);
        }

        if (! is_null($this->onBeforeFieldsRender)) {
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
