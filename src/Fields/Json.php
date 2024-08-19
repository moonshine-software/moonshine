<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\Icon;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\Condition;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Removable;
use MoonShine\Traits\WithFields;
use Throwable;

class Json extends Field implements
    HasFields,
    RemovableContract,
    HasDefaultValue,
    DefaultCanBeArray
{
    use WithFields;
    use Removable;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.json';

    protected bool $keyValue = false;

    protected bool $onlyValue = false;

    protected bool $isGroup = true;

    protected bool $isVertical = false;

    protected bool $isCreatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButton $creatableButton = null;

    protected array $buttons = [];

    protected int $level = 0;

    protected bool $asRelation = false;

    protected bool $isReorderable = true;

    protected bool $isFilterMode = false;

    protected ?ModelResource $asRelationResource = null;

    protected bool $asRelationDeleteWhenEmpty = false;

    protected array $subRelations = [];

    /**
     * @throws Throwable
     */
    public function keyValue(
        string $key = 'Key',
        string $value = 'Value',
        ?Field $keyField = null,
        ?Field $valueField = null,
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;

        $this->fields([
            ($keyField ?? Text::make($key, 'key'))
                ->setColumn('key')
                ->customAttributes($this->attributes()->getAttributes()),

            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
                ->customAttributes($this->attributes()->getAttributes()),
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
        ?Field $valueField = null,
    ): static {
        $this->keyValue = false;
        $this->onlyValue = true;

        $this->fields([
            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
                ->customAttributes($this->attributes()->getAttributes()),
        ]);

        return $this;
    }

    public function isOnlyValue(): bool
    {
        return $this->onlyValue;
    }

    public function isKeyOrOnlyValue(): bool
    {
        return $this->keyValue || $this->onlyValue;
    }

    public function vertical(Closure|bool|null $condition = null): self
    {
        $this->isVertical = Condition::boolean($condition, true);

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?int $limit = null,
        ?ActionButton $button = null
    ): self {
        $this->isCreatable = Condition::boolean($condition, true);

        if ($this->isCreatable()) {
            $this->creatableLimit = $limit;
            $this->creatableButton = $button?->customAttributes([
                '@click.prevent' => 'add()',
            ]);
        }

        return $this;
    }

    public function creatableButton(): ?ActionButton
    {
        return $this->creatableButton;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function creatableLimit(): ?int
    {
        return $this->creatableLimit;
    }

    public function filterMode(): self
    {
        $this->isFilterMode = true;
        $this->creatable(false);

        return $this;
    }

    public function isFilterMode(): bool
    {
        return $this->isFilterMode;
    }

    public function reorderable(Closure|bool|null $condition = null): self
    {
        $this->isReorderable = Condition::boolean($condition, true);

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->isReorderable;
    }

    protected function incrementLevel(): self
    {
        ++$this->level;

        return $this;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function level(): int
    {
        return $this->level;
    }

    /**
     * @throws Throwable
     */
    public function asRelation(ModelResource $resource, bool $deleteWhenEmpty = true): self
    {
        $this->asRelation = true;
        $this->asRelationResource = $resource;
        $this->asRelationDeleteWhenEmpty = $deleteWhenEmpty;

        $this->fields(
            $resource->getFormFields()->onlyFields()
        );

        return $this;
    }

    public function isAsRelation(): bool
    {
        return $this->asRelation;
    }

    public function asRelationResource(): ?ModelResource
    {
        return $this->asRelationResource;
    }

    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): array
    {
        if (array_filter($this->buttons) !== []) {
            return $this->buttons;
        }

        $buttons = [];

        if ($this->isRemovable()) {
            $buttons[] = ActionButton::make('', '#')
                ->icon('heroicons.outline.trash')
                ->onClick(fn ($action): string => 'remove', 'prevent')
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-error'])
                ->showInLine();
        }

        return $buttons;
    }

    public function preparedFields(): Fields
    {
        return $this->getFields()->prepareAttributes()->prepareReindex(parent: $this, before: function (
            Json $parent,
            Field $field
        ): void {
            $field
                ->disableSortable()
                ->setRequestKeyPrefix($parent->requestKeyPrefix());

            throw_if(
                ! $parent->isAsRelation() && $field instanceof ModelRelationField,
                new FieldException(
                    'Relationship fields in JSON field unavailable'
                )
            );
        });
    }

    protected function resolvePreview(): View|string
    {
        if ($this->isRawMode()) {
            return (string) parent::resolvePreview();
        }

        return $this->resolveValue()
            ->simple()
            ->preview()
            ->render();
    }

    protected function reformatFilledValue(mixed $data): mixed
    {
        if ($this->isKeyOrOnlyValue() && ! $this->isFilterMode()) {
            return collect($data)->map(fn ($data, $key): array => $this->extractKeyValue(
                $this->isOnlyValue() ? [$data] : [$key => $data]
            ))->values()->toArray();
        }

        return $data;
    }

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
     * @throws Throwable
     */
    protected function resolveValue(): mixed
    {
        $emptyRow = $this->isAsRelation()
            ? $this->asRelationResource()?->getModel()
            : [];

        // because the TableBuilder filters the values
        if (blank($emptyRow)) {
            $emptyRow = [null];
        }

        $value = $this->isPreviewMode()
            ? $this->toFormattedValue()
            : $this->toValue();

        $values = is_iterable($value)
            ? $value
            : [$value ?? $emptyRow];

        $values = collect($values)->when(
            ! $this->isPreviewMode() && ! $this->isCreatable() && blank($values),
            static fn ($values): Collection => $values->push($emptyRow)
        );

        $fields = $this->preparedFields();
        $sortable = ! $this->isPreviewMode()
            && ! $this->isAsRelation()
            && $this->isReorderable();

        if ($sortable) {
            $fields->prepend(
                Preview::make(
                    formatted: static fn () => Icon::make('heroicons.outline.bars-4')
                )->customAttributes(['class' => 'handle', 'style' => 'cursor: move'])
            );
        }

        return TableBuilder::make($fields, $values)
            ->name("json_{$this->id()}")
            ->customAttributes(
                $this->attributes()
                    ->except(['class', 'data-name', 'data-column'])
                    ->when(
                        $sortable,
                        fn (ComponentAttributeBag $attr): ComponentAttributeBag => $attr->merge([
                            'data-handle' => '.handle',
                        ])
                    )
                    ->jsonSerialize()
            )
            ->when(
                $sortable,
                fn (TableBuilder $table): TableBuilder => $table->sortable()
            )
            ->when(
                $this->isAsRelation(),
                fn (TableBuilder $table): TableBuilder => $table
                    ->cast($this->asRelationResource()?->getModelCast())
            )
            ->when(
                $this->isVertical(),
                fn (TableBuilder $table): TableBuilder => $table->vertical()
            );
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'table' => $this->resolveValue()
                ->editable()
                ->reindex(prepared: true)
                ->when(
                    $this->isCreatable(),
                    fn (TableBuilder $table): TableBuilder => $table->creatable(
                        limit: $this->creatableLimit(),
                        button: $this->creatableButton()
                    )
                )
                ->buttons($this->getButtons())
                ->simple(),
        ];
    }

    /**
     * @throws Throwable
     */
    protected function prepareOnApply(iterable $collection): array
    {
        $collection = collect($collection);

        return $collection->when(
            $this->isKeyOrOnlyValue(),
            fn ($data): Collection => $data->mapWithKeys(
                fn ($data, $key): array => $this->isOnlyValue()
                    ? [$key => $data['value']]
                    : [$data['key'] => $data['value']]
            )
        )->filter(fn ($value): bool => $this->filterEmpty($value))->toArray();
    }

    private function filterEmpty(mixed $value): bool
    {
        if (is_iterable($value) && filled($value)) {
            return collect($value)
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
        bool $fill = false
    ): mixed {
        $requestValues = array_filter($this->requestValue() ?: []);
        $applyValues = [];

        foreach ($requestValues as $index => $values) {
            if ($this->isAsRelation()) {
                $values = $this->asRelationResource()
                    ?->getModel()
                    ?->forceFill($values) ?? $values;

                $requestValues[$index] = $values;
            }

            foreach ($this->preparedFields() as $field) {
                $field->setNameIndex($index);

                $field->when($fill, fn (Field $f): Field => $f->resolveFill($values->toArray(), $values));

                $apply = $callback($field, $values, $data);

                data_set(
                    $applyValues[$index],
                    $field->column(),
                    data_get($apply, $field->column())
                );

                if ($field instanceof self && $field->isAsRelation()) {
                    $this->subRelations[$this->column()][$field->column()] = $field->column();
                }
            }
        }

        $preparedValues = $this->prepareOnApply($applyValues);
        $values = $this->isKeyValue() ? $preparedValues : array_values($preparedValues);

        return is_null($response) ? data_set(
            $data,
            str_replace('.', '->', $this->column()),
            $values
        ) : $response($values, $data);
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: fn (Field $field, mixed $values): mixed => $field->beforeApply($values),
            response: static fn (array $values, mixed $data): mixed => $data
        );
    }

    protected function resolveOnApply(): ?Closure
    {
        if (! $this->parent() instanceof self && $this->isAsRelation()) {
            return static fn (mixed $item): mixed => $item;
        }

        return fn ($item): mixed => $this->resolveAppliesCallback(
            data: $item,
            callback: fn (Field $field, mixed $values): mixed => $field->apply(
                static fn ($data): mixed => data_set($data, $field->column(), data_get($values, $field->column(), '')),
                $values
            ),
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: fn (Field $field, mixed $values): mixed => $this->isAsRelation()
                ? $field->apply(
                    static fn ($data): mixed => data_set($data, $field->column(), data_get($values, $field->column(), '')),
                    $values
                )
                : $field->afterApply($values),
            response: $this->isAsRelation()
                ? fn (array $values, mixed $data) => $this->saveRelation($values, $data)
                : static fn (array $values, mixed $data): mixed => $data,
            fill: $this->isAsRelation(),
        );
    }

    private function saveRelation(array $items, mixed $model, string $relationName = null)
    {
        $items = collect($items);

        $relationName ??= $this->column();

        $related = $model->{$relationName}()->getRelated();

        $relatedKeyName = $related->getKeyName();
        $relatedQualifiedKeyName = $related->getQualifiedKeyName();

        $ids = $items
            ->pluck($relatedKeyName)
            ->filter()
            ->toArray();

        $model->{$relationName}()->when(
            ! empty($ids),
            fn (Builder $q) => $q->whereNotIn(
                $relatedQualifiedKeyName,
                $ids
            )->delete()
        );

        $model->{$relationName}()->when(
            empty($ids) && $this->asRelationDeleteWhenEmpty,
            fn (Builder $q) => $q->delete()
        );

        foreach ($items as $item) {
            $parent = $model->{$relationName}()->updateOrCreate(
                [$relatedQualifiedKeyName => $item[$relatedKeyName] ?? null],
                $item
            );

            if(! empty($this->subRelations[$relationName])) {
                foreach ($this->subRelations[$relationName] as $subRelation) {
                    $this->saveRelation(
                        $item[$subRelation] ?? [],
                        $parent,
                        $subRelation
                    );
                }
            }
        }

        return $model;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        if ($this->isAsRelation() && ! $this->asRelationResource()?->deleteRelationships()) {
            return $data;
        }

        $values = $this->toValue(withDefault: false);

        if (! $this->isKeyOrOnlyValue() && filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        fn (Field $field): mixed => $field
                            ->when(
                                $this->isAsRelation() && $value instanceof Arrayable,
                                fn (Field $f): Field => $f->resolveFill($value->toArray(), $value)
                            )
                            ->when(
                                is_array($value),
                                fn (Field $f): Field => $f->resolveFill($value)
                            )
                            ->afterDestroy($value)
                    );
            }
        }

        return $data;
    }
}
