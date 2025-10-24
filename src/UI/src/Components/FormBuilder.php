<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use JsonException;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\HasButtonsContract;
use MoonShine\Contracts\UI\HasCasterContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Concerns\HasButtons;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Traits\Fields\WithAdditionalFields;
use MoonShine\UI\Traits\HasAsync;
use MoonShine\UI\Traits\HasDataCast;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @template TFields of FieldsContract = FieldsContract
 * @template TData of mixed = mixed
 * @template TCaster of DataCasterContract<TData> = DataCasterContract
 * @template TWrapper of DataWrapperContract<TData> = DataWrapperContract
 * @method static static make(string $action = '', FormMethod $method = FormMethod::POST, FieldsContract|iterable $fields = [], mixed $values = [])
 *
 * @implements HasFieldsContract<TFields>
 * @implements FormBuilderContract<TData>
 * @implements HasCasterContract<TCaster, TWrapper>
 */
final class FormBuilder extends MoonShineComponent implements
    FormBuilderContract,
    HasCasterContract,
    HasButtonsContract,
    HasFieldsContract
{
    use HasAsync;

    /**
     * @use WithAdditionalFields<TFields>
     */
    use WithAdditionalFields;
    use HasDataCast;
    use HasButtons;
    use WithFields;

    protected string $view = 'moonshine::components.form.builder';

    /**
     * @var TData
     */
    protected mixed $values = [];

    /**
     * @var string[]
     */
    protected array $excludeFields = [
        '_redirect',
        '_without-redirect',
        '_method',
        '_component_name',
    ];

    protected bool $isPrecognitive = false;

    protected bool $hideSubmit = false;

    protected bool $raw = false;

    protected bool $errorsAbove = true;

    protected ?ActionButtonContract $submit = null;

    protected ?Closure $onBeforeFieldsRender = null;

    protected Closure|string|null $reactiveUrl = null;

    /**
     * @param  FieldsContract|iterable<array-key, ComponentContract>  $fields
     * @param  TData  $values
     */
    public function __construct(
        protected string $action = '',
        protected FormMethod $method = FormMethod::POST,
        FieldsContract|iterable $fields = [],
        mixed $values = [],
    ) {
        parent::__construct();

        $this->fields($fields);
        $this->fill($values);

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

    protected function prepareFields(): FieldsContract
    {
        $fields = $this->getFields();
        $casted = $this->castData($this->getValues());

        $fields->fill(
            $casted->toArray(),
            $casted,
        );

        $fields->prepareAttributes();

        return $fields;
    }

    public function action(string $action): self
    {
        $this->action = $action;
        $this->setAttribute('action', $action);

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

    protected function prepareAsyncUrl(Closure|string|null $url = null): Closure|string
    {
        return $url ?? $this->getAction();
    }

    public function download(): self
    {
        return $this->customAttributes([
            'data-async-response-type' => 'blob',
        ]);
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
        ?CrudResourceContract $resource = null,
    ): self {
        $asyncUrl = $this->getCore()->getRouter()->getEndpoints()->method(
            $method,
            $message,
            params: ['resourceItem' => $resource?->getItemID()],
            page: $page,
            resource: $resource,
        );

        return $this->action($asyncUrl)->async(
            $asyncUrl,
            events: $events,
            callback: $callback,
        );
    }

    public function asyncSelector(string|array $selector): self
    {
        return $this->customAttributes([
            'data-async-selector' => \is_array($selector) ? implode(',', $selector) : $selector,
        ]);
    }

    /**
     * @param  (Closure(self $ctx): string)|string  $reactiveUrl
     */
    public function reactiveUrl(Closure|string $reactiveUrl): self
    {
        $this->reactiveUrl = $reactiveUrl;

        return $this;
    }

    private function getReactiveUrl(): string
    {
        if (! \is_null($this->reactiveUrl)) {
            return value($this->reactiveUrl, $this);
        }

        return $this->getCore()->getRouter()->getEndpoints()->reactive();
    }

    public function method(FormMethod $method): self
    {
        $this->method = $method;
        $this->setAttribute('method', $method->toString());

        return $this;
    }

    public function getMethod(): FormMethod
    {
        return $this->method;
    }

    public function redirect(?string $uri = null): self
    {
        if (! \is_null($uri)) {
            $this->additionalFields[] = Hidden::make('_redirect')
                ->setValue($uri);
        }

        return $this;
    }

    public function withoutRedirect(): self
    {
        $this->additionalFields[] = Hidden::make('_without-redirect')->setValue(true);

        return $this;
    }

    public function withoutErrorToast(): self
    {
        $this->customAttributes([
            'data-without-error-toast' => true,
        ]);

        return $this;
    }

    public function dispatchEvent(array|string $events, array $exclude = [], bool $withoutPayload = false): self
    {
        $excludes = $withoutPayload ? '*' : implode(',', [
            ...$exclude,
            '_component_name',
            '_token',
            '_method',
        ]);

        return $this->customAttributes([
            '@submit.prevent' => "dispatchEvents(
                `" . AlpineJs::prepareEvents($events) . "`,
                `$excludes`
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

    public function rawMode(): self
    {
        $this->raw = true;

        return $this;
    }

    public function isRaw(): bool
    {
        return $this->raw;
    }

    public function submit(?string $label = null, array $attributes = [], ?ActionButtonContract $button = null): self
    {
        $this->submit = $button ?: $this->getSubmit();

        if (! \is_null($label) && \is_null($button)) {
            $this->submit = $this->getSubmit()->setLabel($label);
        }

        if ($attributes !== [] && \is_null($button)) {
            $this->submit = $this->getSubmit()->customAttributes($attributes);
        }

        return $this;
    }

    public function getSubmit(): ActionButtonContract
    {
        $submit = $this->submit ?: ActionButton::make(
            $this->getCore()->getTranslator()->get('moonshine::ui.save'),
        );

        if ($this->isHideSubmit()) {
            $submit->style('display: none');
        }

        return $submit->customAttributes([
            'type' => 'submit',
        ])->content(
            fn (): Spinner => Spinner::make()
            ->class('js-form-submit-button-loader')
            ->style('display: none')
        );
    }

    public function errorsAbove(bool $enable = true): self
    {
        $this->errorsAbove = $enable;

        return $this;
    }

    protected function hasErrorsAbove(): bool
    {
        return $this->errorsAbove;
    }

    public function switchFormMode(bool $isAsync, string|array|null $events = ''): self
    {
        return $isAsync ? $this->async(events: $events) : $this->precognitive();
    }

    /**
     * @return string[]
     */
    protected function getExcludedFields(): array
    {
        return $this->excludeFields;
    }

    /**
     * @param  string[] $excludeFields
     */
    protected function excludeFields(array $excludeFields): self
    {
        $this->excludeFields = array_merge($this->excludeFields, $excludeFields);

        return $this;
    }

    /**
     * @param  Closure(FieldsContract $fields, static $ctx): FieldsContract  $callback
     */
    public function onBeforeFieldsRender(Closure $callback): self
    {
        $this->onBeforeFieldsRender = $callback;

        return $this;
    }

    /**
     * @param Closure(TData $values, FieldsContract $fields): bool $apply
     * @param null|Closure(FieldContract $field): void $default
     * @param null|Closure(TData $values): TData $before
     * @param null|Closure(TData $values): void $after
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
            $this->getValues(),
        )->getOriginal();

        if (\is_null($default)) {
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
                ->onlyFields(withApplyWrappers: true)
                ->exceptElements(
                    fn (ComponentContract $element): bool => $element instanceof FieldContract && \in_array($element->getColumn(), $this->getExcludedFields(), true),
                );

            $values = \is_null($before) ? $values : $before($values);

            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($values));

            $fields->each(static fn (FieldContract $field): mixed => $field->apply($default($field), $values));

            $apply($values, $fields);

            $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($values));

            value($after, $values);
        } catch (Throwable $e) {
            if ($throw) {
                throw $e;
            }

            return false;
        }

        return true;
    }

    /**
     * @param mixed[] $data
     * @throws Throwable
     */
    protected function showWhenFields(FieldsContract $fields, array &$data): void
    {
        foreach ($fields->whenFieldsConditions() as $whenConditions) {
            foreach ($whenConditions as $value) {
                $data[] = $value;
            }
        }

        foreach ($fields as $field) {
            if ($field instanceof HasFieldsContract) {
                $this->showWhenFields($field->getPreparedFields()->onlyFields(), $data);
            }
        }
    }

    /**
     * by default when using `showWen` hidden data is not sent,
     * but this method changes the behavior and data is sent
     */
    public function submitShowWhenAttribute(): self
    {
        return $this->customAttributes([
            'data-submit-show-when' => 1,
        ]);
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
            fn (FieldContract $field): FieldContract => $field->formName($this->getName()),
        );

        if (! $this->isRaw()) {
            $fields->prepend(
                Hidden::make('_component_name')->formName($this->getName())->setValue($this->getName()),
            );
        }

        $reactiveFields = $onlyFields->reactiveFields()
            ->mapWithKeys(static fn (FieldContract $field): array => [$field->getColumn() => $field->getReactiveValue()]);

        $whenFields = [];
        $this->showWhenFields($onlyFields, $whenFields);

        $xData = json_encode([
            'whenFields' => $whenFields,
            'reactiveUrl' => $reactiveFields->isNotEmpty()
                ? $this->getReactiveUrl()
                : '',
        ], JSON_THROW_ON_ERROR);

        $this->xDataMethod('formBuilder', $this->getName(), $xData, $reactiveFields->toJson());

        $this->customAttributes([
            'data-component' => $this->getName(),
        ]);

        if (! $this->isAsync() && $this->isPrecognitive()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'precognition()',
            ]);
        }

        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::FORM_RESET, $this->getName()) => 'formReset',
            AlpineJs::eventBlade(JsEvent::FORM_SUBMIT, $this->getName()) => 'submit',
            AlpineJs::eventBlade(JsEvent::SHOW_WHEN_REFRESH, $this->getName()) => 'whenFieldsInit',
        ]);

        if ($this->isAsync()) {
            $this->action(
                $this->getAction() ?: $this->getAsyncUrl(),
            );
            $this->customAttributes([
                'x-on:submit.prevent' => 'async(`' . $this->getAsyncEvents(
                ) . '`, ' . json_encode($this->getAsyncCallback(), JSON_THROW_ON_ERROR) . ')',
            ]);
        }

        if (! \is_null($this->onBeforeFieldsRender)) {
            $fields = value($this->onBeforeFieldsRender, $fields, $this);
        }

        return [
            'fields' => $fields,
            'precognitive' => $this->isPrecognitive(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->getAsyncUrl(),
            'buttons' => $this->getButtons($this->castData($this->getValues())),
            'hideSubmit' => $this->isHideSubmit(),
            'submit' => $this->getSubmit(),
            'errors' => $this->getCore()->getRequest()->getFormErrors($this->getName()),
            'errorsAbove' => $this->hasErrorsAbove(),
            'raw' => $this->isRaw(),
        ];
    }
}
