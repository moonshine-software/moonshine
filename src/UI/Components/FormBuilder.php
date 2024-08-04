<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use JsonException;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\UI\ActionButtonsContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Traits\Fields\WithAdditionalFields;
use MoonShine\UI\Traits\HasAsync;
use MoonShine\UI\Traits\HasDataCast;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @method static static make(string $action = '', FormMethod $method = FormMethod::POST, FieldsContract|array $fields = [], mixed $values = [])
 */
final class FormBuilder extends MoonShineComponent implements FormBuilderContract
{
    use HasAsync;
    use WithAdditionalFields;
    use HasDataCast;
    use WithFields;

    protected string $view = 'moonshine::components.form.builder';

    protected mixed $values = [];

    protected iterable $buttons = [];

    protected array $excludeFields = [
        '_force_redirect',
        '_redirect',
        '_method',
        '_component_name',
        '_async_field',
    ];

    protected bool $isPrecognitive = false;

    protected ?string $submitLabel = null;

    protected bool $hideSubmit = false;

    protected MoonShineComponentAttributeBag $submitAttributes;

    protected ?Closure $onBeforeFieldsRender = null;

    protected Closure|string|null $reactiveUrl = null;

    public function __construct(
        protected string $action = '',
        protected FormMethod $method = FormMethod::POST,
        FieldsContract|array $fields = [],
        mixed $values = []
    ) {
        parent::__construct();

        $this->fields($fields);
        $this->fill($values);

        $this->submitAttributes = new MoonShineComponentAttributeBag([
            'type' => 'submit',
        ]);

        $this->customAttributes(array_filter([
            'action' => $this->action,
            'method' => $this->getMethod()->toString(),
            'enctype' => 'multipart/form-data',
        ]));
    }

    public function fill(mixed $values = []): static
    {
        $this->values = $values;

        return $this;
    }

    public function fillCast(mixed $values, DataCasterContract $cast): static
    {
        return $this
            ->cast($cast)
            ->fill($values);
    }

    public function getValues(): mixed
    {
        return $this->values ?? [];
    }

    public function buttons(iterable $buttons = []): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getPreparedFields(): FieldsContract
    {
        $fields = $this->getFields();
        $casted = $this->castData($this->getValues());

        $fields->fill(
            $casted->toArray(),
            $casted
        );

        $fields->prepareAttributes();

        return $fields;
    }

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->buttons)
            ->fill($this->castData($this->getValues()))
            ->onlyVisible()
            ->withoutBulk();
    }

    public function action(string $action): self
    {
        $this->action = $action;
        $this->attributes->set('action', $action);

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

    protected function prepareAsyncUrl(Closure|string|null $url = null): Closure|string|null
    {
        return $url ?? $this->getAction();
    }

    /**
     * @throws Throwable
     */
    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
    ): self {
        $asyncUrl = $this->getCore()->getRouter()->getEndpoints()->asyncMethod(
            $method,
            $message,
            params: ['resourceItem' => $resource?->getItemID()],
            page: $page,
            resource: $resource
        );

        return $this->action($asyncUrl)->async(
            $asyncUrl,
            events: $events,
            callback: $callback
        );
    }

    public function reactiveUrl(Closure|string $reactiveUrl): self
    {
        $this->reactiveUrl = $reactiveUrl;

        return $this;
    }

    private function getReactiveUrl(): string
    {
        if(! is_null($this->reactiveUrl)) {
            return value($this->reactiveUrl, $this);
        }

        return $this->getCore()->getRouter()->getEndpoints()->reactive();
    }

    public function method(FormMethod $method): self
    {
        $this->method = $method;
        $this->attributes->set('method', $method->toString());

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

    public function getMethod(): FormMethod
    {
        return $this->method;
    }

    public function dispatchEvent(array|string $events): self
    {
        return $this->customAttributes([
            '@submit.prevent' => "dispatchEvents(
                `" . AlpineJs::prepareEvents($events) . "`,
                `_component_name,_token,_method`
            )",
        ]);
    }

    public function hideSubmit(): self
    {
        $this->hideSubmit = true;

        return $this;
    }

    public function isHideSubmit(): bool
    {
        return $this->hideSubmit;
    }

    public function submit(string $label, array $attributes = []): self
    {
        $this->submitLabel = $label;
        $this->submitAttributes->setAttributes(
            $attributes + $this->submitAttributes->getAttributes()
        );

        return $this;
    }

    public function getSubmitAttributes(): MoonShineComponentAttributeBag
    {
        return $this->submitAttributes;
    }

    public function getSubmitLabel(): string
    {
        return $this->submitLabel ?? $this->getCore()->getTranslator()->get('moonshine::ui.save');
    }

    public function switchFormMode(bool $isAsync, string|array|null $events = ''): self
    {
        return $isAsync ? $this->async(events: $events) : $this->precognitive();
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
        $values = $this->castData(
            $this->getValues()
        )->getOriginal();

        if (is_null($default)) {
            $default = static fn (FieldContract $field): Closure => static function (mixed $item) use ($field): mixed {
                if (! $field->hasRequestValue() && ! $field->getDefaultIfExists()) {
                    return $item;
                }

                $value = $field->getRequestValue() !== false ? $field->getRequestValue() : null;

                data_set($item, $field->getColumn(), $value);

                return $item;
            };
        }

        try {
            $fields = $this
                ->getPreparedFields()
                ->onlyFields()
                ->exceptElements(
                    fn (FieldContract $element): bool => in_array($element->getColumn(), $this->getExcludedFields(), true)
                );

            $values = is_null($before) ? $values : $before($values);

            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($values));

            $fields
                ->withoutOutside()
                ->each(static fn (FieldContract $field): mixed => $field->apply($default($field), $values));

            $apply($values, $fields);

            $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($values));

            value($after, $values);
        } catch (Throwable $e) {
            report_if(! $throw, $e);
            throw_if($throw, $e);

            return false;
        }

        return true;
    }

    protected function showWhenConditions($elements, array &$data, string $column = null): void
    {
        $parentColumn = $column ?? '';

        foreach ($elements->whenFieldsConditions() as $whenConditions) {
            foreach ($whenConditions as $value) {
                $value['showField'] =
                    $parentColumn
                        ? $parentColumn . '.' . $value['showField']
                        : $value['showField'];

                $data[] = $value;
            }
        }

        foreach ($elements as $element) {
            if($element instanceof HasFieldsContract) {
                $this->showWhenConditions(
                    $element->getFields()->onlyFields(),
                    $data,
                    $parentColumn . $parentColumn ? '.' : '' . $element->getColumn()
                );
            }
        }
    }

    /**
     * @throws Throwable
     * @return array<string, mixed>
     * @throws JsonException
     */
    protected function viewData(): array
    {
        $fields = $this->getPreparedFields();

        if ($this->hasAdditionalFields()) {
            $this->getAdditionalFields()->each(static fn ($field) => $fields->push($field));
        }

        $onlyFields = $fields->onlyFields();
        $onlyFields->each(
            fn (FieldContract $field): FieldContract => $field->formName($this->getName())
        );
        $fields->prepend(
            Hidden::make('_component_name')->setValue($this->getName())
        );

        $reactiveFields = $onlyFields->reactiveFields()
            ->mapWithKeys(static fn (FieldContract $field): array => [$field->getColumn() => $field->getValue()]);

        $whenFields = [];
        $this->showWhenConditions($onlyFields, $whenFields);

        $xData = json_encode([
            'whenFields' => $whenFields,
            'reactiveUrl' => $reactiveFields->isNotEmpty()
                ? $this->getReactiveUrl()
                : '',
        ], JSON_THROW_ON_ERROR);

        $this->customAttributes([
            'x-data' => "formBuilder(`{$this->getName()}`, $xData, {$reactiveFields->toJson()})",
        ]);

        if ($this->isPrecognitive()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'precognition()',
            ]);
        }

        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::FORM_RESET, $this->getName()) => 'formReset',
            AlpineJs::eventBlade(JsEvent::FORM_SUBMIT, $this->getName()) => 'submit',
        ]);

        if ($this->isAsync()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'async(`' . $this->getAsyncEvents(
                ) . '`, `' . $this->getAsyncCallback()?->getSuccess() . '`, `' . $this->getAsyncCallback()?->getBefore() . '`)',
            ]);
        }

        if (! is_null($this->onBeforeFieldsRender)) {
            $fields = value($this->onBeforeFieldsRender, $fields);
        }

        return [
            'fields' => $fields,
            'precognitive' => $this->isPrecognitive(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->getAsyncUrl(),
            'buttons' => $this->getButtons(),
            'hideSubmit' => $this->isHideSubmit(),
            'submitLabel' => $this->getSubmitLabel(),
            'submitAttributes' => $this->getSubmitAttributes(),
            'errors' => $this->getCore()->getRequest()->getFormErrors($this->getName()),
        ];
    }
}
