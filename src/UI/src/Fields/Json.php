<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\RemovableContract;
use MoonShine\UI\Contracts\WrapperWithApplyContract;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Traits\Fields\HasVerticalMode;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Removable;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<TableBuilderContract|FieldsGroup>
 */
class Json extends Field implements
    HasFieldsContract,
    FieldWithComponentContract,
    RemovableContract,
    HasDefaultValueContract,
    CanBeArray
{
    use WithFields;
    use Removable;
    use WithDefaultValue;
    use HasVerticalMode;

    protected string $view = 'moonshine::fields.json';

    protected bool $keyValue = false;

    protected bool $onlyValue = false;

    protected bool $objectMode = false;

    protected bool $isGroup = true;

    protected bool $isCreatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButtonContract $creatableButton = null;

    /**
     * @var list<ActionButtonContract>
     */
    protected array $buttons = [];

    protected bool $isReorderable = true;

    protected bool $isFilterMode = false;

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyRemoveButton = null;

    protected ?Closure $modifyCreateButton = null;

    protected bool $resolveValueOnce = true;

    protected null|TableBuilderContract|FieldsGroup $resolvedComponent = null;

    protected bool $isFilterEmpty = true;

    /**
     * @throws Throwable
     */
    public function keyValue(
        string $key = 'Key',
        string $value = 'Value',
        ?FieldContract $keyField = null,
        ?FieldContract $valueField = null,
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;

        $this->fields([
            ($keyField ?? Text::make($key, 'key'))
                ->setColumn('key')
                ->customAttributes($this->getAttributes()->jsonSerialize()),

            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
                ->customAttributes($this->getAttributes()->jsonSerialize()),
        ]);

        return $this;
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }

    /**
     * @throws Throwable
     */
    public function onlyValue(
        string $value = 'Value',
        ?FieldContract $valueField = null,
    ): static {
        $this->keyValue = false;
        $this->onlyValue = true;

        $this->fields([
            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
                ->customAttributes($this->getAttributes()->jsonSerialize()),
        ]);

        return $this;
    }

    public function isOnlyValue(): bool
    {
        return $this->onlyValue;
    }

    /**
     * @throws Throwable
     */
    public function object(): static
    {
        $this->objectMode = true;

        return $this->customAttributes([
            'class' => 'space-elements',
        ]);
    }

    public function isObjectMode(): bool
    {
        return $this->objectMode;
    }

    public function isKeyOrOnlyValue(): bool
    {
        return $this->keyValue || $this->onlyValue;
    }

    public function isGroup(): bool
    {
        return ! $this->isObjectMode() && $this->isGroup;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?int $limit = null,
        ?ActionButtonContract $button = null,
    ): static {
        $this->isCreatable = value($condition, $this) ?? true;

        if ($this->isCreatable()) {
            $this->creatableLimit = $limit;
            $this->creatableButton = $button?->customAttributes([
                '@click.prevent' => 'add()',
            ]);
        }

        return $this;
    }

    public function getCreateButton(): ?ActionButtonContract
    {
        $button = $this->creatableButton;

        if (! $button instanceof ActionButtonContract) {
            $button = ActionButton::make($this->getCore()->getTranslator()->get('moonshine::ui.add'))
                ->icon('plus-circle')
                ->customAttributes(['@click.prevent' => 'add()', 'class' => 'w-full']);
        }

        if (! \is_null($this->modifyCreateButton)) {
            $button = \call_user_func($this->modifyCreateButton, $button, $this);
        }

        return $button;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function getCreateLimit(): ?int
    {
        return $this->creatableLimit;
    }

    public function filterMode(): static
    {
        $this->isFilterMode = true;
        $this->creatable(false);

        return $this;
    }

    public function isFilterMode(): bool
    {
        return $this->isFilterMode;
    }

    public function reorderable(Closure|bool|null $condition = null): static
    {
        $this->isReorderable = value($condition, $this) ?? true;

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->isReorderable;
    }

    /**
     * @param  Closure(TableBuilder $table, bool $preview): TableBuilder  $callback
     */
    public function modifyTable(Closure $callback): self
    {
        $this->modifyTable = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyRemoveButton(Closure $callback): self
    {
        $this->modifyRemoveButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyCreateButton(Closure $callback): self
    {
        $this->modifyCreateButton = $callback;

        return $this;
    }

    /**
     * @param  list<ActionButtonContract>  $buttons
     */
    public function buttons(array $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return   list<ActionButtonContract>
     */
    public function getButtons(): array
    {
        if (array_filter($this->buttons) !== []) {
            return $this->buttons;
        }

        $buttons = [];

        if ($this->isRemovable()) {
            $button = ActionButton::make()
                ->icon('trash')
                ->onClick(static fn ($action): string => 'remove', 'prevent')
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-error'])
                ->showInLine();

            if (! \is_null($this->modifyRemoveButton)) {
                $button = \call_user_func($this->modifyRemoveButton, $button, $this);
            }

            $buttons[] = $button;
        }

        return $buttons;
    }

    protected function prepareFields(): FieldsContract
    {
        $fields = $this->getFields();

        if (! $this->isPreviewMode()) {
            $fields->prepareAttributes();
        }

        if ($this->isObjectMode()) {
            $fields = $fields
                ->map(
                    fn ($field) => $field
                        ->customAttributes($this->getReactiveAttributes("{$this->getColumn()}.{$field->getColumn()}"))
                        ->customAttributes(['data-object-mode' => true])
                );
        }

        $fields
            ->onlyFields()
            ->prepareReindexNames(parent: $this, before: static function (self $parent, FieldContract $field): void {
                if (! $field->getParent() instanceof WrapperWithApplyContract && ! $parent->isObjectMode()) {
                    $field->withoutWrapper();
                } else {
                    $parent->customWrapperAttributes([
                        'class' => 'inner-json-object-mode',
                        'data-object-mode' => true,
                    ]);
                }

                $field->setRequestKeyPrefix($parent->getRequestKeyPrefix());
            }, except: fn (FieldContract $parent): bool => $parent instanceof self && $parent->isObjectMode());


        return $fields;
    }

    protected function resolveRawValue(): mixed
    {
        if (\is_array($this->rawValue)) {
            return json_encode($this->rawValue, JSON_THROW_ON_ERROR);
        }

        return (string) $this->rawValue;
    }

    protected function resolvePreview(): Renderable|string
    {
        return $this->getComponent()
            ->simple()
            ->preview()
            ->render();
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        if (\is_string($data)) {
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }

        if ($this->isKeyOrOnlyValue() && ! $this->isFilterMode()) {
            /** @var Collection<array-key, mixed> $collection */
            $collection = new Collection($data);

            return $collection->map(fn (mixed $data, int|string $key): array => $this->extractKeyValue(
                $this->isOnlyValue() ? [$data] : [$key => $data],
            ))
                ->values()
                ->toArray();
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function extractKeyValue(array $data): array
    {
        if ($this->isKeyValue()) {
            return [
                'key' => key($data) ?? '',
                'value' => $data[key($data)] ?? '',
            ];
        }

        if ($this->isOnlyValue()) {
            return [
                'value' => $data[key($data)] ?? '',
            ];
        }

        return $data;
    }

    protected function isBlankValue(): bool
    {
        if ($this->isPreviewMode()) {
            return parent::isBlankValue();
        }

        return blank($this->value);
    }

    /**
     * @param iterable<string, mixed> $collection
     * @return array<string, mixed>
     * @throws Throwable
     */
    public function prepareOnApplyRecursive(iterable $collection): array
    {
        $collection = $this->prepareOnApply($collection);

        foreach ($this->getFields() as $field) {
            if ($field instanceof File) {
                $column = $field->getColumn();

                $collection = array_map(static fn (array $data): array => [
                    ...$data,
                    $column => $data[$field->getHiddenColumn()] ?? null,
                ], $collection);
            }

            if ($field instanceof self) {
                foreach ($collection as $index => $value) {
                    $column = $field->getColumn();
                    $collection[$index][$column] = $field->prepareOnApplyRecursive(
                        $value[$column] ?? []
                    );
                }
            }
        }

        return $collection;
    }

    /**
     * @throws Throwable
     */
    protected function resolveOldValue(mixed $old): mixed
    {
        return $this->prepareOnApplyRecursive($old);
    }

    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        $value = $this->isPreviewMode()
            ? $this->toFormattedValue()
            : $this->getValue();


        /** @var Collection<array-key, mixed> $values */
        $values = new Collection(
            is_iterable($value)
                ? $value
                : [],
        );

        $fields = $this->getPreparedFields();

        if ($this->isObjectMode() && ! $this->isPreviewMode()) {
            return FieldsGroup::make(
                Fields::make($fields)->fillCloned($values->toArray())
            )->mapFields(
                fn (FieldContract $field): FieldContract => $field
                    ->formName($this->getFormName())
                    ->setParent($this),
            );
        }

        $values = $values->when(
            ! $this->isPreviewMode() && ! $this->isCreatable() && blank($values),
            static fn ($values): Collection => $values->push([null]),
        );

        $reorderable = ! $this->isPreviewMode() && $this->isReorderable();

        if ($reorderable) {
            $fields->prepend(
                Preview::make(
                    column: '__handle',
                    formatted: static fn () => Icon::make('bars-4'),
                )->customAttributes(['class' => 'handle', 'style' => 'cursor: move']),
            );
        }

        if ($this->isPreviewMode() && $this->isObjectMode()) {
            $values = [$values];
        }

        $component = TableBuilder::make($fields, $values)
            ->name('repeater_' . $this->getColumn())
            ->inside('field')
            ->customAttributes(
                $this->getAttributes()
                    ->except(['class', 'data-name', 'data-column'])
                    ->when(
                        $reorderable,
                        static fn (ComponentAttributesBagContract $attr): ComponentAttributesBagContract => $attr->merge([
                            'data-handle' => '.handle',
                        ]),
                    )
                    ->jsonSerialize()
            )
            ->customAttributes(['data-validation-wrapper' => true])
            ->when(
                $reorderable,
                static fn (TableBuilderContract $table): TableBuilderContract => $table->reorderable(),
            )
            ->when(
                ($this->isObjectMode() && $this->isPreviewMode()) || $this->isVertical(),
                fn (TableBuilderContract $table): TableBuilderContract => $table->vertical(
                    title: $reorderable ? fn (FieldContract $field, ComponentContract $default): Column => Column::make([
                        $field->getColumn() === '__handle' ? $field : Div::make([
                            $field->getLabel(),
                        ]),
                    ])->columnSpan($this->verticalTitleSpan) : null,
                    value: $reorderable ? fn (FieldContract $field, ComponentContract $default): Column => $field->getColumn() === '__handle'
                        ? Column::make()->columnSpan($this->verticalValueSpan)
                        /** @var Column $default */
                        /** @phpstan-ignore-next-line  */
                        : $default->columnSpan($this->verticalValueSpan)->customAttributes(['data-validation-wrapper' => true]) : null,
                ),
            )
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilder $tableBuilder) => value($this->modifyTable, $tableBuilder, $this->isPreviewMode()),
            );

        if (! $this->isPreviewMode()) {
            $component = $component
                ->editable()
                ->reindex(prepared: true)
                ->when(
                    $this->isCreatable(),
                    fn (TableBuilderContract $table): TableBuilderContract => $table->creatable(
                        limit: $this->getCreateLimit(),
                        button: $this->getCreateButton(),
                    )->removeAfterClone(),
                )
                ->buttons($this->getButtons())
                ->simple();
        }

        return $this->resolvedComponent = $component;
    }

    /**
     * @param iterable<array-key, mixed> $collection
     * @return array<array-key, mixed>
     *
     * @throws Throwable
     */
    public function prepareOnApply(iterable $collection): array
    {
        $collection = new Collection($collection);

        return $collection->when(
            $this->isKeyOrOnlyValue(),
            fn (Collection $data): Collection => $data->mapWithKeys(
                fn (array $data, string|int $key): array => $this->isOnlyValue()
                    ? [$key => $data['value']]
                    : [$data['key'] => $data['value']],
            ),
        )
            ->filter(fn ($value): bool => $this->filterEmpty($value))
            ->when(
                $this->isReorderable() && ! $this->isObjectMode() && ! $this->isKeyValue(),
                static fn (Collection $data) => $data->sortKeys()
            )
            ->toArray();
    }

    public function isFilterEmpty(): bool
    {
        return $this->isFilterEmpty;
    }

    public function stopFilteringEmpty(): static
    {
        $this->isFilterEmpty = false;

        return $this;
    }

    private function filterEmpty(mixed $value): bool
    {
        if (! $this->isFilterEmpty()) {
            return true;
        }

        if (is_iterable($value) && filled($value)) {
            $collection = new Collection($value);

            return $collection
                ->filter(fn ($v): bool => $this->filterEmpty($v))
                ->isNotEmpty();
        }

        return ! blank($value);
    }

    /**
     * @throws Throwable
     */
    protected function resolveAppliesCallback(
        mixed $data,
        Closure $callback,
        ?Closure $response = null,
        bool $fill = false,
    ): mixed {
        $requestValues = array_filter($this->getRequestValue() ?: []);
        $applyValues = [];

        if ($this->isObjectMode()) {
            $requestValues = [$requestValues];
        }

        foreach ($requestValues as $index => $values) {
            foreach ($this->resetPreparedFields()->getPreparedFields() as $field) {
                if (! $field->isCanApply()) {
                    continue;
                }

                if (! $this->isObjectMode()) {
                    $field->setNameIndex($index);
                }

                $field->when($fill, static fn (FieldContract $f): FieldContract => $f->fillData($values));

                $apply = $callback($field, $values, $data);

                if ($field instanceof WrapperWithApplyContract) {
                    $applyValues[$index] = $apply;

                    continue;
                }

                data_set(
                    /** @phpstan-ignore-next-line  */
                    $applyValues[$index],
                    $field->getColumn(),
                    data_get($apply, $field->getColumn()),
                );
            }

            if ($this->isObjectMode()) {
                $applyValues = $applyValues[$index] ?? [];
            }
        }

        $preparedValues = $this->prepareOnApply($applyValues);
        $values = $this->isObjectMode() || $this->isKeyValue()
            ? $preparedValues
            : array_values($preparedValues);

        return \is_null($response) ? data_set(
            $data,
            str_replace('.', '->', $this->getColumn()),
            $values,
        ) : $response($values, $data);
    }

    protected function resolveOnApply(): ?Closure
    {
        return fn ($item): mixed => $this->resolveAppliesCallback(
            data: $item,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->apply(
                static fn ($data): mixed => data_set($data, $field->getColumn(), data_get($values, $field->getColumn(), '')),
                $values,
            ),
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->beforeApply($values),
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->afterApply($values),
            response: static fn (array $values, mixed $data): mixed => $data,
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $values = $this->toValue(withDefault: false);

        if (! $this->isKeyOrOnlyValue() && filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        static fn (FieldContract $field): mixed => $field
                            ->fillData($value)
                            ->afterDestroy($value),
                    );
            }
        }

        return $data;
    }

    public function getReactiveValue(): mixed
    {
        if (! $this->isObjectMode()) {
            throw FieldException::reactivityNotSupported(static::class, 'without object mode');
        }

        return $this->toValue() ?? $this->getPreparedFields()
            ->onlyFields()
            ->mapWithKeys(fn (FieldContract $field): array => [$field->getColumn() => null]);
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'component' => $this->getComponent(),
        ];
    }
}
