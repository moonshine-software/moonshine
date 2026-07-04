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
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @implements HasFieldsContract<Fields|FieldsContract>
 */
class Json extends Field implements CanBeArray, HasDefaultValueContract, HasFieldsContract
{
    use WithDefaultValue;
    use WithFields {
        WithFields::fields as protected fieldsFromTrait;
    }

    protected string $view = 'moonshine::fields.json';

    protected bool $isGroup = true;

    protected bool $removable = true;

    protected bool $creatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButtonContract $createButton = null;

    protected bool $hideCreateButton = false;

    protected bool $showCreateButtonText = true;

    protected bool $showCreateButtonIcon = true;

    /**
     * @var null|Closure(ActionButtonContract, self): ActionButtonContract
     */
    protected ?Closure $modifyCreateButton = null;

    /**
     * @var null|Closure(ActionButton, self): ActionButton
     */
    protected ?Closure $modifyRemoveButton = null;

    /**
     * @var iterable<array-key, ActionButtonContract>
     */
    protected iterable $buttons = [];

    /**
     * @var array<string, mixed>
     */
    protected array $removeButtonAttributes = [];

    protected bool $reorderable = false;

    protected bool $keyValue = false;

    protected bool $onlyValue = false;

    protected bool $object = false;

    protected bool $filterEmpty = true;

    protected bool $table = false;

    /**
     * @var null|Closure(TableBuilder, bool): TableBuilder
     */
    protected ?Closure $modifyTable = null;

    protected string $orientation = 'horizontal';

    protected ?string $emptyMessage = null;

    /**
     * @param  FieldsContract|(Closure(static): iterable<array-key, ComponentContract>)|iterable<array-key, ComponentContract>  $fields
     *
     * @throws Throwable
     */
    public function fields(FieldsContract|Closure|iterable $fields, string $orientation = 'horizontal'): static
    {
        return $this->setFields($fields, $orientation);
    }

    /**
     * @param  FieldsContract|(Closure(static): iterable<array-key, ComponentContract>)|iterable<array-key, ComponentContract>  $fields
     *
     * @throws Throwable
     */
    protected function setFields(FieldsContract|Closure|iterable $fields, string $orientation = 'horizontal'): static
    {
        $this->resetPreparedFields();
        $this->orientation($orientation);

        return $this->fieldsFromTrait($fields);
    }

    public function orientation(string $orientation): static
    {
        $this->orientation = \in_array($orientation, ['horizontal', 'vertical'], true)
            ? $orientation
            : 'horizontal';

        return $this;
    }

    public function vertical(bool $condition = true): static
    {
        return $this->orientation($condition ? 'vertical' : 'horizontal');
    }

    /**
     * @throws Throwable
     */
    public function keyValue(
        string|FieldContract $key = 'Key',
        string|FieldContract $value = 'Value',
        ?FieldContract $keyField = null,
        ?FieldContract $valueField = null,
        string $orientation = 'horizontal',
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;
        $this->object = false;

        $keyField ??= $key instanceof FieldContract ? $key : Text::make($key);
        $valueField ??= $value instanceof FieldContract ? $value : Text::make($value);

        return $this->setFields([
            $keyField,
            $valueField,
        ], $orientation);
    }

    /**
     * @throws Throwable
     */
    public function onlyValue(string $value = 'Value', ?FieldContract $valueField = null): static
    {
        $this->onlyValue = true;
        $this->keyValue = false;
        $this->object = false;

        return $this->setFields([$valueField ?? Text::make($value)]);
    }

    public function isKeyOrOnlyValue(): bool
    {
        return $this->isKeyValue() || $this->isOnlyValue();
    }

    public function isKeyValue(): bool
    {
        return $this->keyValue;
    }

    public function isOnlyValue(): bool
    {
        return $this->onlyValue;
    }

    public function object(bool $condition = true): static
    {
        $this->object = $condition;

        if ($condition) {
            $this->keyValue = false;
            $this->onlyValue = false;
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function removable(Closure|bool|null $condition = null, array $attributes = []): static
    {
        $this->removable = value($condition, $this) ?? true;
        $this->removeButtonAttributes = $attributes;

        return $this;
    }

    public function creatableLimit(?int $limit = null): static
    {
        $this->creatableLimit = $limit;

        return $this;
    }

    /**
     * @param  Closure(ActionButtonContract $button, self $field): ActionButtonContract  $callback
     */
    public function modifyCreateButton(Closure $callback): static
    {
        $this->modifyCreateButton = $callback;

        return $this;
    }

    /**
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyRemoveButton(Closure $callback): static
    {
        $this->modifyRemoveButton = $callback;

        return $this;
    }

    public function filterMode(): static
    {
        $this->creatable(false);

        return $this;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?int $limit = null,
        ?ActionButtonContract $button = null,
        bool $hideButton = false,
        bool $showButtonText = true,
        bool $showButtonIcon = true,
    ): static {
        $this->creatable = value($condition, $this) ?? true;
        $this->creatableLimit = $limit;
        $this->createButton = $button;
        $this->hideCreateButton = $hideButton;
        $this->showCreateButtonText = $showButtonText;
        $this->showCreateButtonIcon = $showButtonIcon;

        return $this;
    }

    /**
     * @param  iterable<array-key, ActionButtonContract>  $buttons
     */
    public function buttons(iterable $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function reorderable(bool $condition = true): static
    {
        $this->reorderable = $condition;

        return $this;
    }

    public function stopFilteringEmpty(bool $condition = true): static
    {
        $this->filterEmpty = ! $condition;

        return $this;
    }

    public function table(bool $condition = true): static
    {
        $this->table = $condition;

        if ($condition) {
            $this->previewMode();
        }

        return $this;
    }

    /**
     * @param  Closure(TableBuilder $table, bool $preview): TableBuilder  $callback
     */
    public function modifyTable(Closure $callback): static
    {
        $this->modifyTable = $callback;

        return $this;
    }

    public function emptyMessage(string $message): static
    {
        $this->emptyMessage = $message;

        return $this;
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function reformatFilledValue(mixed $data): array
    {
        if ($this->getParent() instanceof Fieldset) {
            return $this->decodeFilledArray($data);
        }

        return $this->normalizeRows($data);
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function decodeFilledArray(mixed $value): array
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        if (\is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = \is_array($decoded) ? $decoded : [];
        }

        return \is_array($value) ? $value : [];
    }

    protected function resolveRawValue(): mixed
    {
        $value = parent::resolveRawValue();

        return \is_array($value) ? json_encode($value) : $value;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function normalizeRows(mixed $value): array
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        if (\is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = \is_array($decoded) ? $decoded : [];
        }

        if (! \is_array($value)) {
            return [];
        }

        if ($value === []) {
            return [];
        }

        if ($this->isKeyValue()) {
            return array_is_list($value)
                ? $this->normalizeKeyValueListRows($value)
                : $this->normalizeKeyValueRows($value);
        }

        if ($this->isOnlyValue()) {
            return $this->normalizeOnlyValueRows($value);
        }

        if ($this->isObject() && ! array_is_list($value)) {
            return [$this->normalizeRow($value)];
        }

        $keyValuePayloadRows = $this->previewKeyValuePayloadRows($value);

        if (! $this->isKeyOrOnlyValue() && $this->keyValuePayloadMatchesFields($keyValuePayloadRows)) {
            return [$this->normalizeRow($this->rowFromKeyValuePayload($keyValuePayloadRows))];
        }

        if (! array_is_list($value)) {
            $value = [$value];
        }

        return array_values(array_map(
            fn (mixed $row): array => $this->normalizeRow(\is_array($row) ? $row : []),
            $value,
        ));
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @return list<array<string, mixed>>
     */
    protected function normalizeKeyValueRows(array $value): array
    {
        $schema = $this->fieldsSchema();
        $keyField = $schema[0] ?? null;
        $valueField = $schema[1] ?? null;

        if ($keyField === null || $valueField === null) {
            return [];
        }

        return array_values(array_map(
            fn (mixed $itemValue, int|string $itemKey): array => $this->normalizeRow([
                $keyField['column'] => $itemKey,
                $valueField['column'] => $itemValue,
            ]),
            $value,
            array_keys($value),
        ));
    }

    /**
     * @param  list<mixed>  $value
     * @return list<array<string, mixed>>
     */
    protected function normalizeKeyValueListRows(array $value): array
    {
        $schema = $this->fieldsSchema();
        $keyField = $schema[0] ?? null;
        $valueField = $schema[1] ?? null;

        if ($keyField === null || $valueField === null) {
            return [];
        }

        return array_values(array_map(
            fn (mixed $row): array => $this->normalizeRow(
                $this->resolveCompatibleKeyValueRow(\is_array($row) ? $row : [], $keyField, $valueField),
            ),
            $value,
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function fieldsSchema(): array
    {
        return $this->getFields()
            ->onlyFields()
            ->map(fn (FieldContract $field): array => $this->fieldSchema($field))
            ->values()
            ->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function fieldSchema(FieldContract $field): array
    {
        $schema = [
            'column' => $field->getColumn(),
            'label' => $field->getLabel(),
            'type' => $this->fieldType($field),
            'multiple' => method_exists($field, 'isMultiple') && $field->isMultiple(),
            'nullable' => $field->isNullable(),
            'options' => $this->fieldOptions($field),
            'placeholder' => (string) ($field->getAttribute('placeholder') ?? $field->getLabel()),
            'wrapperClass' => (string) $field->getWrapperAttributes()->get('class', ''),
            'default' => $field instanceof HasDefaultValueContract ? $field->getDefault() : null,
        ];

        if ($field instanceof self) {
            $schema['fields'] = $field->fieldsSchema();
            $schema['removable'] = $field->isRemovable();
            $schema['creatable'] = $field->isCreatable();
            $schema['creatableLimit'] = $field->getCreatableLimit();
            $schema['hideCreateButton'] = $field->isCreateButtonHidden();
            $schema['showCreateButtonText'] = $field->isCreateButtonTextShown();
            $schema['showCreateButtonIcon'] = $field->isCreateButtonIconShown();
            $schema['createButton'] = $field->renderCreateButton(
                '__moonshine_json_add__',
                '__moonshine_json_disabled__',
            );
            $schema['buttons'] = $field->renderButtons('__moonshine_json_remove__');
            $schema['removeButton'] = $field->renderRemoveButton('__moonshine_json_remove__');
            $schema['removeButtonAttributes'] = $field->getRemoveButtonAttributes();
            $schema['reorderable'] = $field->isReorderable();
            $schema['orientation'] = $field->getOrientation();
            $schema['keyValue'] = $field->isKeyValue();
            $schema['onlyValue'] = $field->isOnlyValue();
            $schema['objectMode'] = $field->isObject();
            $schema['filterEmpty'] = $field->isFilteringEmpty();
            $schema['tableMode'] = $field->isTable();
            $schema['emptyMessage'] = $field->getEmptyMessage();
        }

        return $schema;
    }

    protected function fieldType(FieldContract $field): string
    {
        return match (true) {
            $field instanceof self => 'json',
            $field instanceof Number => 'number',
            $field instanceof Textarea => 'textarea',
            $field instanceof Select => 'select',
            $field instanceof Switcher => 'switcher',
            $field instanceof Checkbox => 'checkbox',
            $field instanceof Date => 'date',
            $field instanceof Color => 'color',
            $field instanceof Email => 'email',
            $field instanceof Url => 'url',
            $field instanceof Phone => 'tel',
            default => 'text',
        };
    }

    /**
     * @return list<array<string, string>>
     */
    protected function fieldOptions(FieldContract $field): array
    {
        if (! method_exists($field, 'getValues')) {
            return [];
        }

        return $this->normalizeOptions($field->getValues()->toArray());
    }

    /**
     * @param  array<array-key, mixed>  $options
     * @return list<array<string, string>>
     */
    protected function normalizeOptions(array $options): array
    {
        $normalized = [];

        foreach ($options as $value => $label) {
            if (\is_array($label) && \array_key_exists('values', $label) && \is_array($label['values'])) {
                array_push($normalized, ...$this->normalizeOptions($label['values']));

                continue;
            }

            if (\is_array($label) && ! \array_key_exists('value', $label) && ! \array_key_exists('label', $label)) {
                array_push($normalized, ...$this->normalizeOptions($label));

                continue;
            }

            $normalized[] = \is_array($label)
                ? [
                    'value' => (string) ($label['value'] ?? $value),
                    'label' => (string) ($label['label'] ?? $label['value'] ?? $value),
                ]
                : [
                    'value' => (string) $value,
                    'label' => (string) $label,
                ];
        }

        return $normalized;
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }

    public function isCreatable(): bool
    {
        return $this->creatable;
    }

    public function getCreatableLimit(): ?int
    {
        return $this->creatableLimit;
    }

    public function isCreateButtonHidden(): bool
    {
        return $this->hideCreateButton;
    }

    public function isCreateButtonTextShown(): bool
    {
        return $this->showCreateButtonText;
    }

    public function isCreateButtonIconShown(): bool
    {
        return $this->showCreateButtonIcon;
    }

    protected function renderCreateButton(string $onClick, string $disabled = '! canAdd()'): ?string
    {
        if (! ($this->createButton instanceof ActionButtonContract) && ! ($this->modifyCreateButton instanceof Closure)) {
            return null;
        }

        $label = $this->getCore()->getTranslator()->get('moonshine::ui.add');

        $button = $this->createButton instanceof ActionButtonContract
            ? clone $this->createButton
            : ActionButton::make($this->isCreateButtonTextShown() ? $label : '')
                ->rawMode()
                ->customAttributes([
                    'type' => 'button',
                    'class' => 'btn btn-primary json-field__add',
                ]);

        if (! ($this->createButton instanceof ActionButtonContract) && $this->isCreateButtonIconShown()) {
            $button->icon('plus');
        }

        $button->customAttributes([
            'x-on:click.prevent' => $onClick,
            'x-bind:disabled' => $disabled,
        ]);

        if ($this->modifyCreateButton instanceof Closure) {
            $button = ($this->modifyCreateButton)($button, $this);
        }

        return (string) $button->render();
    }

    protected function renderButtons(string $removeExpression): ?string
    {
        $html = '';

        foreach ($this->buttons as $button) {
            $html .= str_replace(
                'remove()',
                $removeExpression,
                (string) (clone $button)->render(),
            );
        }

        return $html === '' ? null : $html;
    }

    protected function renderRemoveButton(string $onClick): ?string
    {
        if (! $this->modifyRemoveButton instanceof Closure) {
            return null;
        }

        $removeButtonAttributes = $this->getRemoveButtonAttributes();
        $hasCustomRemoveClick = \array_key_exists('@click.prevent', $removeButtonAttributes)
            || \array_key_exists('x-on:click.prevent', $removeButtonAttributes);

        $button = ActionButton::make('')
            ->icon('trash')
            ->customAttributes(array_merge([
                'type' => 'button',
                'class' => 'btn btn-error json-field__remove',
            ], $removeButtonAttributes));

        if (! $hasCustomRemoveClick) {
            $button->customAttributes([
                'x-on:click.prevent' => $onClick,
            ]);
        }

        $button = ($this->modifyRemoveButton)($button, $this);

        return (string) $button->render();
    }

    /**
     * @return array<string, mixed>
     */
    public function getRemoveButtonAttributes(): array
    {
        return $this->removeButtonAttributes;
    }

    public function isReorderable(): bool
    {
        return $this->reorderable;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    public function isObject(): bool
    {
        return $this->object;
    }

    public function isFilteringEmpty(): bool
    {
        return $this->filterEmpty;
    }

    public function isTable(): bool
    {
        return $this->table;
    }

    public function getEmptyMessage(): string
    {
        return $this->emptyMessage ?? $this->getCore()->getTranslator()->get('moonshine::ui.empty');
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($this->fieldsSchema() as $field) {
            $normalized[$field['column']] = $this->normalizeValue(
                $row[$field['column']] ?? null,
                $field,
            );
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function normalizeValue(mixed $value, array $field): mixed
    {
        if ($value === null && \array_key_exists('default', $field)) {
            $value = $field['default'];
        }

        if (($field['type'] ?? null) === 'json') {
            return $this->normalizeJsonValue($value, $field);
        }

        if (($field['multiple'] ?? false) === true) {
            return \is_array($value) ? array_values($value) : [];
        }

        if (\in_array($field['type'] ?? null, ['checkbox', 'switcher'], true)) {
            return filter_var($value, FILTER_VALIDATE_BOOL);
        }

        if (($field['type'] ?? null) === 'select') {
            return $this->normalizeSelectValue($value, $field);
        }

        if (($field['type'] ?? null) === 'number') {
            return $value === null || $value === '' ? null : (is_numeric($value) ? $value + 0 : null);
        }

        return $value === null ? '' : (string) $value;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    protected function normalizeJsonValue(mixed $value, array $field): array
    {
        if (\is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = \is_array($decoded) ? $decoded : [];
        }

        if (! \is_array($value)) {
            $value = [];
        }

        if (($field['keyValue'] ?? false) === true && ! array_is_list($value)) {
            return $this->normalizeNestedKeyValueRows($value, $field);
        }

        if (($field['onlyValue'] ?? false) === true) {
            return $this->normalizeNestedOnlyValueRows($value, $field);
        }

        if (($field['objectMode'] ?? false) === true && ! array_is_list($value)) {
            return [$this->normalizeNestedRow($value, $field)];
        }

        if (($field['objectMode'] ?? false) === true) {
            return array_values(array_map(
                fn (mixed $row): array => $this->normalizeNestedRow(\is_array($row) ? $row : [], $field),
                $value,
            ));
        }

        if (! array_is_list($value)) {
            $value = [$value];
        }

        return array_values(array_map(
            fn (mixed $row): array => $this->normalizeNestedRow(\is_array($row) ? $row : [], $field),
            $value,
        ));
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    protected function normalizeNestedKeyValueRows(array $value, array $field): array
    {
        $keyField = ($field['fields'] ?? [])[0] ?? null;
        $valueField = ($field['fields'] ?? [])[1] ?? null;

        if (! \is_array($keyField) || ! \is_array($valueField)) {
            return [];
        }

        return array_values(array_map(
            fn (mixed $itemValue, int|string $itemKey): array => $this->normalizeNestedRow([
                $keyField['column'] => $itemKey,
                $valueField['column'] => $itemValue,
            ], $field),
            $value,
            array_keys($value),
        ));
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    protected function normalizeNestedRow(array $row, array $field): array
    {
        $normalized = [];

        foreach (($field['fields'] ?? []) as $nestedField) {
            if (! \is_array($nestedField)) {
                continue;
            }

            $normalized[$nestedField['column']] = $this->normalizeValue(
                $row[$nestedField['column']] ?? null,
                $nestedField,
            );
        }

        return $normalized;
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    protected function normalizeNestedOnlyValueRows(array $value, array $field): array
    {
        $valueField = ($field['fields'] ?? [])[0] ?? null;

        if (! \is_array($valueField)) {
            return [];
        }

        $values = array_is_list($value) ? $value : array_values($value);

        return array_values(array_map(
            fn (mixed $itemValue): array => $this->normalizeNestedRow(\is_array($itemValue)
                ? $itemValue
                : [$valueField['column'] => $itemValue], $field),
            $values,
        ));
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function normalizeSelectValue(mixed $value, array $field): string
    {
        if (($field['nullable'] ?? false) === false && ($value === null || $value === '')) {
            $firstOption = ($field['options'] ?? [])[0]['value'] ?? null;

            if ($firstOption !== null) {
                return (string) $firstOption;
            }
        }

        return $value === null ? '' : (string) $value;
    }

    /**
     * @param  array<array-key, mixed>  $value
     * @return list<array<string, mixed>>
     */
    protected function normalizeOnlyValueRows(array $value): array
    {
        $valueField = $this->fieldsSchema()[0] ?? null;

        if ($valueField === null) {
            return [];
        }

        $values = array_is_list($value) ? $value : array_values($value);

        return array_values(array_map(
            fn (mixed $itemValue): array => $this->normalizeRow(\is_array($itemValue)
                ? $itemValue
                : [$valueField['column'] => $itemValue]),
            $values,
        ));
    }

    protected function resolvePreview(): Renderable|string
    {
        /** @phpstan-var view-string $view */
        $view = 'moonshine::components.json.preview';

        return view($view, [
            'label' => $this->getLabel(),
            'items' => $this->resolvePreviewItems(),
            'objectMode' => $this->isObject(),
            'tableMode' => $this->isTable(),
            ...$this->resolveTableViewData(preview: true),
        ]);
    }

    /**
     * @return list<array{fields: list<array<string, mixed>>}>
     */
    protected function resolvePreviewItems(): array
    {
        $rawValue = $this->toFormattedValue();
        $rows = $this->normalizeRows($rawValue);
        $fields = $this->previewFieldsSchema();

        if ($this->isKeyValue()) {
            return $this->previewKeyValueItems($rows, $fields);
        }

        return $this->previewItems($rows, $fields);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array<string, mixed>>  $fields
     * @return list<array{fields: list<array<string, mixed>>}>
     */
    protected function previewItems(array $rows, array $fields): array
    {
        return array_values(array_map(
            fn (array $row): array => [
                'fields' => $this->previewFields($row, $fields),
            ],
            $rows,
        ));
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array<string, mixed>>  $fields
     * @return list<array{fields: list<array<string, mixed>>}>
     */
    protected function previewKeyValueItems(array $rows, array $fields): array
    {
        $keyField = $fields[0] ?? null;
        $valueField = $fields[1] ?? null;

        if ($keyField === null || $valueField === null) {
            return [];
        }

        return [
            [
                'fields' => array_values(array_map(
                    function (array $row) use ($keyField, $valueField): array {
                        $row = $this->resolveCompatibleKeyValueRow($row, $keyField, $valueField);

                        return [
                            ...$this->previewField($valueField, $row[$valueField['column']] ?? null),
                            'label' => $this->formatKeyPreviewLabel($row[$keyField['column']] ?? null, $keyField),
                        ];
                    },
                    $rows,
                )),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $keyField
     * @param  array<string, mixed>  $valueField
     * @return array<string, mixed>
     */
    protected function resolveCompatibleKeyValueRow(array $row, array $keyField, array $valueField): array
    {
        $keyColumn = (string) ($keyField['column'] ?? 'key');
        $valueColumn = (string) ($valueField['column'] ?? 'value');

        if (\array_key_exists($keyColumn, $row) && \array_key_exists($valueColumn, $row)) {
            return $row;
        }

        [$keyFound, $keyValue] = $this->findCompatibleColumnValue($row, $keyColumn);
        [$valueFound, $valueValue] = $this->findCompatibleColumnValue($row, $valueColumn);
        $orderedValues = array_values($row);

        if (! $keyFound && \array_key_exists(0, $orderedValues)) {
            $keyFound = true;
            $keyValue = $orderedValues[0];
        }

        if (! $valueFound && \array_key_exists(1, $orderedValues)) {
            $valueFound = true;
            $valueValue = $orderedValues[1];
        }

        if (! $keyFound && ! $valueFound) {
            return $row;
        }

        return [
            ...$row,
            $keyColumn => $keyValue,
            $valueColumn => $valueValue,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{0: bool, 1: mixed}
     */
    protected function findCompatibleColumnValue(array $row, string $column): array
    {
        foreach ($row as $rowColumn => $value) {
            if ($this->isCompatibleColumn((string) $rowColumn, $column)) {
                return [true, $value];
            }
        }

        return [false, null];
    }

    protected function isCompatibleColumn(string $candidate, string $column): bool
    {
        $candidate = str_replace('-', '_', $candidate);
        $column = str_replace('-', '_', $column);

        return $candidate !== $column && str_starts_with($candidate, "{$column}_");
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function formatKeyPreviewLabel(mixed $value, array $field): string
    {
        $label = $this->formatPreviewValue($value, $field);
        $rawValue = (string) $value;

        if ($label !== $rawValue || $rawValue === '') {
            return $label;
        }

        return ucwords(str_replace(['_', '-'], ' ', $rawValue));
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    protected function hasKeyValueRows(array $rows): bool
    {
        if ($rows === []) {
            return false;
        }

        foreach ($rows as $row) {
            if (! \array_key_exists('key', $row) || ! \array_key_exists('value', $row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function previewKeyValuePayloadRows(mixed $value): array
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        if (\is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = \is_array($decoded) ? $decoded : [];
        }

        if (! \is_array($value)) {
            return [];
        }

        if ($value === []) {
            return [];
        }

        if ($this->hasKeyValueRows([$value])) {
            return [$value];
        }

        if (! array_is_list($value)) {
            return [];
        }

        return array_values(array_filter($value, \is_array(...)));
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    protected function keyValuePayloadMatchesFields(array $rows): bool
    {
        if (! $this->hasKeyValueRows($rows)) {
            return false;
        }

        $columns = array_flip(array_map(
            fn (array $field): string => (string) ($field['column'] ?? ''),
            $this->fieldsSchema(),
        ));

        foreach ($rows as $row) {
            if (! \array_key_exists((string) ($row['key'] ?? ''), $columns)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    protected function rowFromKeyValuePayload(array $rows): array
    {
        $row = [];

        foreach ($rows as $item) {
            $row[(string) $item['key']] = $item['value'] ?? null;
        }

        return $row;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  list<array<string, mixed>>  $fields
     * @return list<array<string, mixed>>
     */
    protected function previewFields(array $row, array $fields): array
    {
        $previewFields = [];

        foreach ($fields as $field) {
            $previewFields[] = $this->previewField(
                $field,
                $row[$field['column']] ?? null,
            );
        }

        return $previewFields;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    protected function previewField(array $field, mixed $value): array
    {
        if (($field['type'] ?? null) === 'json') {
            $rows = \is_array($value) ? $value : [];

            return [
                'label' => $this->previewLabel($field),
                'type' => 'json',
                'isBoolean' => false,
                'isEmpty' => $this->isEmptyValue($rows),
                'items' => $this->previewItems($rows, $this->previewNestedFields($field)),
                'objectMode' => ($field['objectMode'] ?? false) === true,
                'tableMode' => ($field['tableMode'] ?? false) === true,
                'tableAttributes' => $field['tableAttributes'] ?? null,
                'tableBuilder' => $field['tableBuilder'] ?? null,
                'tableSimple' => ($field['tableSimple'] ?? false) === true,
                'tableSticky' => ($field['tableSticky'] ?? false) === true,
            ];
        }

        $isBoolean = \in_array($field['type'] ?? null, ['checkbox', 'switcher'], true);

        return [
            'label' => $this->previewLabel($field),
            'type' => $field['type'] ?? 'text',
            'isBoolean' => $isBoolean,
            'value' => $isBoolean ? filter_var($value, FILTER_VALIDATE_BOOL) : $this->formatPreviewValue($value, $field),
            'isEmpty' => ! $isBoolean && $this->isEmptyValue($value),
        ];
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function previewLabel(array $field): string
    {
        return (string) ($field['label'] ?? $field['column'] ?? '');
    }

    protected function isEmptyValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (! \is_array($value)) {
            return false;
        }

        if ($value === []) {
            return true;
        }

        foreach ($value as $nestedValue) {
            if (! $this->isEmptyValue($nestedValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $field
     * @return list<array<string, mixed>>
     */
    protected function previewNestedFields(array $field): array
    {
        return array_values(array_filter(
            $field['fields'] ?? [],
            \is_array(...),
        ));
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function formatPreviewValue(mixed $value, array $field): string
    {
        if ($this->isEmptyValue($value)) {
            return '';
        }

        if (($field['type'] ?? null) === 'select') {
            return $this->formatSelectPreviewValue($value, $field);
        }

        if (\is_array($value)) {
            try {
                return json_encode(
                    $value,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT,
                );
            } catch (Throwable) {
                return '';
            }
        }

        return (string) $value;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function formatSelectPreviewValue(mixed $value, array $field): string
    {
        if (\is_array($value)) {
            return implode(', ', array_map(
                fn (mixed $item): string => $this->selectPreviewLabel($item, $field),
                $value,
            ));
        }

        return $this->selectPreviewLabel($value, $field);
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function selectPreviewLabel(mixed $value, array $field): string
    {
        foreach ($field['options'] ?? [] as $option) {
            if (! \is_array($option)) {
                continue;
            }

            if ((string) ($option['value'] ?? '') === (string) $value) {
                return (string) ($option['label'] ?? $value);
            }
        }

        return (string) $value;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function previewFieldsSchema(): array
    {
        return $this->getFields()
            ->onlyFields()
            ->map(fn (FieldContract $field): array => $this->previewFieldSchema($field))
            ->values()
            ->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function previewFieldSchema(FieldContract $field): array
    {
        $schema = $this->fieldSchema($field);

        if ($field instanceof self) {
            $schema['fields'] = $field->previewFieldsSchema();
            $schema = [
                ...$schema,
                ...$field->resolveTableViewData(preview: true),
            ];
        }

        return $schema;
    }

    /**
     * @return array{
     *     tableAttributes: ?ComponentAttributesBagContract,
     *     tableBuilder: ?TableBuilder,
     *     tableSimple: bool,
     *     tableSticky: bool
     * }
     */
    protected function resolveTableViewData(bool $preview): array
    {
        if (! $this->modifyTable instanceof Closure) {
            return [
                'tableAttributes' => null,
                'tableBuilder' => null,
                'tableSimple' => false,
                'tableSticky' => false,
            ];
        }

        $table = TableBuilder::make()->preview();
        $modifiedTable = ($this->modifyTable)($table, $preview);
        $table = $modifiedTable instanceof TableBuilder ? $modifiedTable : $table;

        return [
            'tableAttributes' => $table->getAttributes(),
            'tableBuilder' => $table,
            'tableSimple' => $table->isSimple(),
            'tableSticky' => $table->isSticky(),
        ];
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        return $this->prepareValueOnApplyRecursive($value);
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function prepareValueOnApply(mixed $value): array
    {
        return $this->prepareOnApply($this->normalizeRows($value));
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function prepareValueOnApplyRecursive(mixed $value): array
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }

        if (\is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = \is_array($decoded) ? $decoded : [];
        }

        if (! \is_array($value)) {
            return [];
        }

        return $this->prepareOnApplyRecursive($value);
    }

    /**
     * @param  iterable<array-key, mixed>  $rows
     * @return array<array-key, mixed>
     */
    public function prepareOnApply(iterable $rows): array
    {
        $rows = $rows instanceof Collection
            ? $rows->toArray()
            : (\is_array($rows) ? $rows : iterator_to_array($rows));

        if ($this->isKeyValue()) {
            return $this->prepareKeyValueOnApply($rows);
        }

        if ($this->isOnlyValue()) {
            return $this->prepareOnlyValueOnApply($rows);
        }

        if ($this->isObject()) {
            return $this->prepareObjectOnApply($rows);
        }

        return $this->prepareRowsOnApply($rows);
    }

    /**
     * @param  iterable<array-key, mixed>  $rows
     * @return array<array-key, mixed>
     */
    public function prepareOnApplyRecursive(iterable $rows): array
    {
        $rows = $rows instanceof Collection
            ? $rows->toArray()
            : (\is_array($rows) ? $rows : iterator_to_array($rows));

        return $this->prepareOnApply($this->prepareRecursiveRowsBeforeApply($rows));
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @return array<array-key, mixed>
     */
    protected function prepareRecursiveRowsBeforeApply(array $rows): array
    {
        if (! array_is_list($rows)) {
            return $this->isKeyOrOnlyValue()
                ? $rows
                : $this->prepareRecursiveRowBeforeApply($rows);
        }

        return array_values(array_map(
            fn (mixed $row): array => $this->prepareRecursiveRowBeforeApply(\is_array($row) ? $row : []),
            $rows,
        ));
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function prepareRecursiveRowBeforeApply(array $row): array
    {
        foreach ($this->getFields() as $field) {
            if ($field instanceof File) {
                $column = $field->getColumn();
                $row[$column] = $row[$field->getHiddenColumn()] ?? $row[$column] ?? null;

                continue;
            }

            if ($field instanceof self) {
                $column = $field->getColumn();
                $row[$column] = $field->prepareOnApplyRecursive($row[$column] ?? []);
            }
        }

        return $row;
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @return array<string, mixed>
     */
    public function prepareKeyValueOnApply(array $rows): array
    {
        $schema = $this->fieldsSchema();
        $keyField = $schema[0] ?? null;
        $valueField = $schema[1] ?? null;

        if ($keyField === null || $valueField === null) {
            return [];
        }

        $result = [];

        foreach ($this->normalizeRows($rows) as $row) {
            $key = (string) ($row[$keyField['column']] ?? '');

            if ($key === '' && $this->isFilteringEmpty()) {
                continue;
            }

            $result[$key] = $this->prepareFieldValueOnApply($row[$valueField['column']] ?? null, $valueField);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $field
     */
    protected function prepareFieldValueOnApply(mixed $value, array $field): mixed
    {
        if (($field['type'] ?? null) !== 'json') {
            return $value;
        }

        $rows = \is_array($value) ? $value : [];

        if (($field['keyValue'] ?? false) === true) {
            return $this->prepareNestedKeyValueOnApply($rows, $field);
        }

        if (($field['onlyValue'] ?? false) === true) {
            return $this->prepareNestedOnlyValueOnApply($rows, $field);
        }

        if (($field['objectMode'] ?? false) === true) {
            return $this->prepareNestedObjectOnApply($rows, $field);
        }

        return $this->filterPreparedRows(array_map(
            fn (array $row): array => $this->prepareNestedRowOnApply($row, $field),
            $this->normalizeJsonValue($rows, $field),
        ), $field);
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    protected function prepareNestedKeyValueOnApply(array $rows, array $field): array
    {
        $keyField = ($field['fields'] ?? [])[0] ?? null;
        $valueField = ($field['fields'] ?? [])[1] ?? null;

        if (! \is_array($keyField) || ! \is_array($valueField)) {
            return [];
        }

        $result = [];

        foreach ($this->normalizeJsonValue($rows, $field) as $row) {
            $key = (string) ($row[$keyField['column']] ?? '');

            if ($key === '' && $this->shouldFilterEmpty($field)) {
                continue;
            }

            $result[$key] = $this->prepareFieldValueOnApply($row[$valueField['column']] ?? null, $valueField);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>|null  $field
     */
    protected function shouldFilterEmpty(?array $field = null): bool
    {
        if ($field === null) {
            return $this->isFilteringEmpty();
        }

        return ($field['filterEmpty'] ?? true) === true;
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @param  array<string, mixed>  $field
     * @return list<mixed>
     */
    protected function prepareNestedOnlyValueOnApply(array $rows, array $field): array
    {
        $valueField = ($field['fields'] ?? [])[0] ?? null;

        if (! \is_array($valueField)) {
            return [];
        }

        $values = array_map(
            fn (array $row): mixed => $this->prepareFieldValueOnApply($row[$valueField['column']] ?? null, $valueField),
            $this->normalizeJsonValue($rows, $field),
        );

        if (! $this->shouldFilterEmpty($field)) {
            return array_values($values);
        }

        return array_values(array_filter(
            $values,
            fn (mixed $value): bool => ! $this->isEmptyValue($value),
        ));
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @param  array<string, mixed>  $field
     * @return array<array-key, mixed>
     */
    protected function prepareNestedObjectOnApply(array $rows, array $field): array
    {
        $rows = $this->normalizeJsonValue($rows, $field);

        if (\count($rows) <= 1) {
            $row = $rows[0] ?? [];

            if ($row === []) {
                return [];
            }

            $prepared = $this->prepareNestedRowOnApply($row, $field);

            return $this->shouldFilterEmpty($field) && $this->isEmptyValue($prepared)
                ? []
                : $prepared;
        }

        return $this->filterPreparedRows(array_map(
            fn (array $row): array => $this->prepareNestedRowOnApply($row, $field),
            $rows,
        ), $field);
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $field
     * @return array<string, mixed>
     */
    protected function prepareNestedRowOnApply(array $row, array $field): array
    {
        $prepared = [];

        foreach (($field['fields'] ?? []) as $nestedField) {
            if (! \is_array($nestedField)) {
                continue;
            }

            $prepared[$nestedField['column']] = $this->prepareFieldValueOnApply(
                $row[$nestedField['column']] ?? null,
                $nestedField,
            );
        }

        return $prepared;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>|null  $field
     * @return list<array<string, mixed>>
     */
    protected function filterPreparedRows(array $rows, ?array $field = null): array
    {
        if (! $this->shouldFilterEmpty($field)) {
            return array_values($rows);
        }

        return array_values(array_filter(
            $rows,
            fn (array $row): bool => ! $this->isEmptyValue($row),
        ));
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @return list<mixed>
     */
    public function prepareOnlyValueOnApply(array $rows): array
    {
        $valueField = $this->fieldsSchema()[0] ?? null;

        if ($valueField === null) {
            return [];
        }

        $values = array_map(
            fn (array $row): mixed => $this->prepareFieldValueOnApply($row[$valueField['column']] ?? null, $valueField),
            $this->normalizeRows($rows),
        );

        if (! $this->isFilteringEmpty()) {
            return array_values($values);
        }

        return array_values(array_filter(
            $values,
            fn (mixed $value): bool => ! $this->isEmptyValue($value),
        ));
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @return array<array-key, mixed>
     */
    public function prepareObjectOnApply(array $rows): array
    {
        $rows = $this->normalizeRows($rows);

        if (\count($rows) <= 1) {
            $row = $rows[0] ?? [];

            if ($row === []) {
                return [];
            }

            $prepared = $this->prepareRowOnApply($row);

            return $this->isFilteringEmpty() && $this->isEmptyValue($prepared)
                ? []
                : $prepared;
        }

        return $this->prepareRowsOnApply($rows);
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    protected function prepareRowOnApply(array $row): array
    {
        $prepared = [];

        foreach ($this->fieldsSchema() as $field) {
            $prepared[$field['column']] = $this->prepareFieldValueOnApply(
                $row[$field['column']] ?? null,
                $field,
            );
        }

        return $prepared;
    }

    /**
     * @param  array<array-key, mixed>  $rows
     * @return list<array<string, mixed>>
     */
    protected function prepareRowsOnApply(array $rows): array
    {
        return $this->filterPreparedRows(array_map(
            fn (array $row): array => $this->prepareRowOnApply($row),
            $this->normalizeRows($rows),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'rows' => $this->normalizeRows($this->getValue()),
            'fields' => $this->fieldsSchema(),
            'controls' => $this->fieldsControls(),
            'inputName' => str_replace('[]', '', $this->getNameAttribute()),
            'removable' => $this->isRemovable(),
            'creatable' => $this->isCreatable(),
            'creatableLimit' => $this->getCreatableLimit(),
            'hideCreateButton' => $this->isCreateButtonHidden(),
            'showCreateButtonText' => $this->isCreateButtonTextShown(),
            'showCreateButtonIcon' => $this->isCreateButtonIconShown(),
            'createButton' => $this->renderCreateButton('add()'),
            'buttons' => $this->renderButtons('remove(rowIndex)'),
            'removeButton' => $this->renderRemoveButton('remove(rowIndex)'),
            'removeButtonAttributes' => $this->getRemoveButtonAttributes(),
            'reorderable' => $this->isReorderable(),
            'orientation' => $this->getOrientation(),
            'keyValue' => $this->isKeyValue(),
            'onlyValue' => $this->isOnlyValue(),
            'objectMode' => $this->isObject(),
            'filterEmpty' => $this->isFilteringEmpty(),
            'emptyMessage' => $this->getEmptyMessage(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function fieldsControls(string $rowVariable = 'row', string $rowPath = 'rowIndex', int $depth = 0): array
    {
        return $this->getFields()
            ->onlyFields()
            ->map(fn (FieldContract $field): array => $this->fieldControl($field, $rowVariable, $rowPath, $depth))
            ->values()
            ->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    protected function fieldControl(FieldContract $field, string $rowVariable, string $rowPath, int $depth): array
    {
        $control = $this->fieldSchema($field);

        if ($field instanceof self) {
            $nestedRowVariable = "nestedRow{$depth}";
            $nestedRowIndex = "nestedRowIndex{$depth}";

            $control['nestedRowVariable'] = $nestedRowVariable;
            $control['nestedRowIndex'] = $nestedRowIndex;
            $control['controls'] = $field->fieldsControls(
                $nestedRowVariable,
                $rowPath . " + '.' + " . json_encode($field->getColumn(), JSON_THROW_ON_ERROR) . " + '.' + {$nestedRowIndex}",
                $depth + 1,
            );

            return $control;
        }

        $control['html'] = $this->renderFieldControl($field, $rowVariable, $rowPath);

        return $control;
    }

    protected function renderFieldControl(FieldContract $field, string $rowVariable, string $rowPath): string
    {
        $control = clone $field;
        $column = $field->getColumn();
        $model = $rowVariable . '[' . json_encode($column, JSON_THROW_ON_ERROR) . ']';
        $path = $rowPath . " + '.' + " . json_encode($column, JSON_THROW_ON_ERROR);

        $control
            ->flushRenderCache()
            ->setNameAttribute($this->virtualFieldName($column))
            ->customAttributes([
                'x-bind:data-json-row-path' => $path,
                'x-model' => $model,
                'x-on:input' => 'sync()',
                'x-on:change' => 'sync()',
            ], override: true);

        return (string) $control->render();
    }

    protected function virtualFieldName(string $column): string
    {
        return '__moonshine_json[' . $this->getIdentity() . '][' . $column . ']';
    }

    protected function resolveOnApply(): ?Closure
    {
        return fn (mixed $item, mixed $value): mixed => data_set(
            $item,
            str_replace('.', '->', $this->getColumn()),
            $this->prepareValueOnApply($value),
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function prepareRequestRows(): array
    {
        $value = $this->getRequestValue();

        return \is_array($value) ? $value : [];
    }
}
