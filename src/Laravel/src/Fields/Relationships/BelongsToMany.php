<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Crud\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Crud\Contracts\Fields\HasPivotContract;
use MoonShine\Crud\Contracts\Fields\HasRelatedValuesContact;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Traits\Fields\BelongsToOrManyCreatable;
use MoonShine\Laravel\Traits\Fields\HasHorizontalMode;
use MoonShine\Laravel\Traits\Fields\HasPivotModalModeConcern;
use MoonShine\Laravel\Traits\Fields\HasTreeMode;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\Laravel\Traits\Fields\WithRelatedLink;
use MoonShine\Laravel\Traits\Fields\WithRelatedValues;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Stringify;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Traits\Fields\ConfigurableSelect;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @template-covariant R of \Illuminate\Database\Eloquent\Relations\BelongsToMany<Model, Model, covariant \Illuminate\Database\Eloquent\Relations\Pivot> = \Illuminate\Database\Eloquent\Relations\BelongsToMany<Model, Model>
 *
 * @implements HasAsyncSearchContract<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model, mixed>, \MoonShine\Laravel\Http\Requests\Relations\RelationModelFieldRequest>
 * @extends ModelRelationField<R>
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<TableBuilderContract|ComponentContract|ActionButtonContract>
 */
class BelongsToMany extends ModelRelationField implements
    HasRelatedValuesContact,
    HasPivotContract,
    HasFieldsContract,
    HasAsyncSearchContract,
    FieldWithComponentContract
{
    use WithFields;
    use WithRelatedValues;
    use Searchable;
    use WithAsyncSearch;
    use HasTreeMode;
    use HasPlaceholder;
    use WithRelatedLink;
    use BelongsToOrManyCreatable;
    use HasHorizontalMode;
    use ConfigurableSelect;
    use HasPivotModalModeConcern;

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected array $translates = [
        'search' => 'moonshine::ui.search',
    ];

    protected bool $isGroup = true;

    protected bool $resolveValueOnce = true;

    protected string $treeParentColumn = '';

    protected bool $onlyCount = false;

    protected bool $inLine = false;

    /** @var null|(Closure(TableBuilderContract, bool): TableBuilderContract) */
    protected ?Closure $modifyTable = null;

    /**
     * @var null|(Closure(mixed, mixed, self): Link)
     */
    protected ?Closure $inLineLink = null;

    protected string $inLineSeparator = '';

    /**
     * @var bool|(Closure(mixed, mixed, self): Badge|bool)
     */
    protected Closure|bool $inLineBadge = false;

    protected bool $selectMode = false;

    /**
     * @var list<ActionButtonContract>
     */
    protected array $buttons = [];

    protected ?string $columnLabel = null;

    protected ?ComponentContract $resolvedComponent = null;

    protected bool $isDeduplicate = true;

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function deduplication(Closure|bool|null $condition = null): static
    {
        $this->isDeduplicate = value($condition, $this) ?? true;

        return $this;
    }

    public function isDeduplicate(): bool
    {
        return $this->isDeduplicate;
    }

    public function onlyCount(): static
    {
        $this->onlyCount = true;

        return $this;
    }

    /**
     * @param  bool|(Closure(mixed $item, mixed $value, self $ctx): Badge|bool)  $badge
     * @param  null|(Closure(mixed $item, mixed $value, self $ctx): Link) $link
     */
    public function inLine(string $separator = '', Closure|bool $badge = false, ?Closure $link = null): static
    {
        $this->inLine = true;
        $this->inLineSeparator = $separator;
        $this->inLineBadge = $badge;
        $this->inLineLink = $link;

        return $this;
    }

    public function selectMode(): static
    {
        $this->selectMode = true;

        return $this;
    }

    public function isSelectMode(): bool
    {
        return $this->selectMode;
    }

    /**
     * @param list<ActionButtonContract> $buttons
     */
    public function buttons(array $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function withCheckAll(): static
    {
        return $this->buttons([
            ActionButton::make('')
                ->onClick(static fn (): string => 'checkAll', 'prevent')
                ->primary()
                ->icon('check'),

            ActionButton::make('')
                ->onClick(static fn (): string => 'uncheckAll', 'prevent')
                ->error()
                ->icon('x-mark'),
        ]);
    }

    public function getButtons(): ActionButtonsContract
    {
        return ActionButtons::make($this->buttons);
    }

    /**
     * @param  Closure(TableBuilderContract $table, bool $preview): TableBuilderContract  $callback
     */
    public function modifyTable(Closure $callback): static
    {
        $this->modifyTable = $callback;

        return $this;
    }

    public function getPivotAs(): string
    {
        return $this->getRelation()?->getPivotAccessor() ?? 'pivot';
    }

    public function getComponentEvent(): JsEvent
    {
        if ($this->isPivotCardsMode()) {
            return JsEvent::CARDS_UPDATED;
        }

        return JsEvent::TABLE_UPDATED;
    }

    public function getTableComponentName(): string
    {
        return 'belongs_to_many_' . $this->getRelationName();
    }

    public function getRelatedKeyName(): string
    {
        return $this->getRelation()?->getRelated()?->getKeyName() ?? 'id';
    }

    /**
     * @return EloquentCollection<array-key, mixed>
     */
    // @phpstan-ignore generics.notSubtype (The legacy collection also carries raw filter IDs, not only models.)
    public function getCollectionValue(): EloquentCollection
    {
        return EloquentCollection::wrap($this->getValue() ?? []);
    }

    /**
     * @return array<array-key, int|string>
     */
    public function getSelectedValue(): string|array
    {
        $selected = $this->isValueWithModels()
            ? $this->getCollectionValue()->pluck($this->getRelatedKeyName())
            : $this->getCollectionValue();

        /** @var array<array-key, int|string> */
        return $selected->all();
    }

    protected function isValueWithModels(mixed $data = null): bool
    {
        $data = Collection::wrap($data ?? $this->toValue());

        if ($data->isEmpty()) {
            return false;
        }

        return $data->every(static fn ($item): bool => $item instanceof Model);
    }

    public function columnLabel(string $label): static
    {
        $this->columnLabel = $label;

        return $this;
    }

    public function getResourceColumnLabel(): string
    {
        return $this->columnLabel ?? $this->getResourceOrFail()->getTitle();
    }

    public function getPivotName(): string
    {
        return "{$this->getRelationName()}_pivot";
    }

    /**
     * @throws Throwable
     */
    protected function prepareFields(): FieldsContract
    {
        return $this->getFields()->prepareAttributes()->prepareReindexNames(
            parent: $this,
            before: fn (self $parent, Field $field): Field => (clone $field)
                ->setColumn("{$this->getPivotAs()}.{$field->getColumn()}")
                ->class('js-pivot-field')
                ->withoutWrapper(),
            performName: fn (string $name): string => str_replace($this->getRelationName(), $this->getPivotName(), $name),
        );
    }

    protected function prepareFill(array $raw = [], ?DataWrapperContract $casted = null): mixed
    {
        $values = parent::prepareFill($raw, $casted);

        if (! $values instanceof EloquentCollection) {
            return EloquentCollection::wrap($values);
        }

        return $values;
    }

    public function setValues(array $values): void
    {
        $this->setValue(new Collection($values));
    }

    public function getAvailableValues(): mixed
    {
        if (! \is_null($this->memoizeValues)) {
            return $this->memoizeValues;
        }

        // fix for filters
        if ($this->isAsyncSearch() && ! $this->isValueWithModels($this->memoizeValues) && filled($this->toValue())) {
            $keys = $this->isSelectMode() ? $this->getCollectionValue()->toArray() : $this->getCollectionValue()->keys();

            $this->memoizeValues = $this->getRelation()
                   ?->getRelated()
                   ?->newQuery()
                   ?->findMany($keys) ?? EloquentCollection::make();
        }

        if ($this->isSelectMode()) {
            return $this->getValues()->toArray();
        }

        if ($this->isTree() || $this->isHorizontalMode()) {
            return $this->getKeys();
        }

        /** @var Collection<array-key, Model> $values */
        $values = $this->memoizeValues ?? ($this->isAsyncSearch() ? Collection::wrap($this->toValue()) : $this->resolveValuesQuery()->get());

        return $values->map(function ($value) {
            if (! $this->isValueWithModels()) {
                return $value
                    ->setRelations([
                        $this->getPivotAs() => [],
                    ]);
            }

            if ($this->isDeduplicate() === false) {
                return $value;
            }

            /** @var Model|null $checked */
            $checked = Collection::wrap($this->toValue())
                ->first(static fn (mixed $item): bool => $item instanceof Model && $item->getKey() === $value->getKey());

            return $value
                ->setRelations($checked?->getRelations() ?? $value->getRelations());
        });
    }

    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        if ($this->isPivotModalMode()) {
            return $this->resolvedComponent = $this->getPivotModalTable();
        }

        /** @var Collection<array-key, Model> $values */
        $values = $this->getAvailableValues();

        if ($this->isRelatedLink()) {
            return $this->getRelatedLink();
        }

        $removeAfterClone = false;

        if (! $this->isPreviewMode() && $this->isAsyncSearch() && blank($values)) {
            $values->push($this->getResourceOrFail()->getDataInstance());
            $removeAfterClone = true;
        }

        $titleColumn = $this->getResourceColumn();

        $checkedColumn = $this->getNameAttribute('${index0}');

        $identityField = Checkbox::make('#', $this->getRelatedKeyName())
            ->simpleMode()
            ->wrapperClass('w-10')
            ->customAttributes($this->getReactiveAttributes())
            ->withoutWrapper()
            ->class('js-pivot-checker')
            ->setNameAttribute($checkedColumn)
            ->formName($this->getFormName())
            ->iterableAttributes();

        $fields = $this->getPreparedFields()
            ->prepend(
                Preview::make($this->getResourceColumnLabel(), $titleColumn, $this->getFormattedValueCallback())
                    ->withoutWrapper()
                    ->formName($this->getFormName())
                    ->class('js-pivot-title'),
            )
            ->prepend($identityField);

        return $this->resolvedComponent = TableBuilder::make(items: $values)
            ->when(
                ! $this->isDeduplicate(),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->withoutKey(),
            )
            ->name($this->getTableComponentName())
            ->customAttributes($this->getAttributes()->jsonSerialize())
            ->fields($fields)
            ->when(
                $removeAfterClone,
                static fn (TableBuilderContract $table): TableBuilderContract => $table->removeAfterClone(),
            )
            ->cast($this->getResourceOrFail()->getCaster())
            ->simple()
            ->editable()
            ->reindex(prepared: true)
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, false),
            )
            ->withNotFound();
    }

    protected function getColumnOrFormattedValue(Model $item, string|int $default): string|int
    {
        if (! \is_null($this->getFormattedValueCallback())) {
            /** @var string|int */
            return \call_user_func(
                $this->getFormattedValueCallback(),
                $item,
                0,
                $this,
            );
        }

        return $default;
    }

    protected function resolveOldValue(mixed $old): mixed
    {
        // otherwise you will have to make a db query to receive records by keys
        if ($this->isAsyncSearch()) {
            return $this->toValue();
        }

        /** @var array<array-key, array<string, mixed>> $oldPivot */
        $oldPivot = $this->getCore()->getRequest()->getOld($this->getPivotName());

        /** @var Collection<array-key, int|string|null> $keys */
        $keys = Collection::wrap($old);

        return $keys
            ->map(fn (int|string|null $key): Model => clone ($this->makeRelatedModel($key, relations: $oldPivot[$key] ?? [], related: $this->getRelation()?->getRelated()) ?? throw \MoonShine\Laravel\Exceptions\ModelRelationFieldException::relationRequired()))
            ->values();
    }

    protected function resolveValue(): mixed
    {
        if (\is_array($this->toValue())) {
            /** @var Collection<array-key, int|string|null> $keys */
            $keys = Collection::wrap($this->toValue());
            $this->setValue(
                $keys
                    ->map(fn (int|string|null $key): Model => clone ($this->makeRelatedModel($key, related: $this->getRelation()?->getRelated()) ?? throw \MoonShine\Laravel\Exceptions\ModelRelationFieldException::relationRequired()))
                    ->values()
            );
        }

        return parent::resolveValue();
    }

    protected function resolveRawValue(): mixed
    {
        return $this->getCollectionValue()
            ->map(static fn (mixed $item): mixed => $item instanceof Model ? $item->getKey() : $item)
            ->toJson();
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
        /** @var EloquentCollection<array-key, Model> $values */
        $values = $this->getCollectionValue();
        $column = $this->getResourceColumn();

        if ($this->isRelatedLink()) {
            return (string) $this->getRelatedLink(preview: true);
        }

        if ($this->onlyCount) {
            return (string) $values->count();
        }

        if ($this->inLine) {
            return $values->implode(function (Model $item) use ($column) {
                $value = $this->getColumnOrFormattedValue($item, Stringify::value(data_get($item, $column, '')));

                if (! \is_null($this->inLineLink)) {
                    /** @var Link|string $linkValue */
                    $linkValue = \call_user_func($this->inLineLink, $item, $value, $this);

                    $value = $linkValue instanceof Link
                        ? $linkValue
                        : Link::make(
                            $linkValue,
                            (string) $value,
                        );
                }

                /** @var Badge|bool $badgeValue */
                $badgeValue = value($this->inLineBadge, $item, $value, $this);

                if ($badgeValue !== false) {
                    $badge = $badgeValue instanceof Badge
                        ? $badgeValue
                        : Badge::make((string) $value, Color::PRIMARY);

                    return (string) $badge->customAttributes(['class' => 'm-1']);
                }

                return $value;
            }, $this->inLineSeparator);
        }

        $fields = $this->getPreparedFields()
            ->prepend(Text::make($this->getResourceColumnLabel(), $column, $this->getFormattedValueCallback()))
            ->prepend(ID::make());

        return (string) TableBuilder::make($fields, $values)
            ->preview()
            ->simple()
            ->cast($this->getResourceOrFail()->getCaster())
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, false),
            );
    }

    /**
     * @return Collection<array-key, int|string>
     */
    public function getCheckedKeys(): Collection
    {
        /** @var Collection<array-key, int|string> $requestValues */
        $requestValues = Collection::wrap($this->getRequestValue() ?: []);

        if ($this->isSelectMode() || $this->isTree() || $this->isHorizontalMode()) {
            return $requestValues;
        }

        if ($this->isDeduplicate() === false) {
            return $requestValues;
        }

        return $requestValues->keys();

    }

    /**
     * @return array<array-key, int|string>
     */
    public function getKeys(): array
    {
        if (\is_null($this->getValue())) {
            return [];
        }

        if ($this->isValueWithModels()) {
            /** @var array<array-key, int|string> */
            return $this->getCollectionValue()->modelKeys();
        }

        return $this->getCollectionValue()->keys()->all();
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn (Model $item): Model => $item;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        if ($this->isPivotModalMode()) {
            return $data;
        }

        /** @var Model $item */
        $item = $data;

        $checkedKeys = $this->getCheckedKeys();
        $simpleSync = $this->isSelectMode() || $this->isTree() || $this->isHorizontalMode() || $this->getFields()->isEmpty();

        if ($simpleSync && self::$silentApply) {
            data_set($item, $this->getRelationName(), $checkedKeys);

            return $item;
        }

        if ($simpleSync && self::$silentApply === false) {
            $this->getRelationFor($item)->sync($checkedKeys);

            return $item;
        }

        $applyValues = [];

        foreach ($checkedKeys as $index => $key) {
            foreach ($this->resetPreparedFields()->getPreparedFields() as $field) {
                if (! $field->isCanApply()) {
                    continue;
                }

                $field->setNameIndex(
                    $this->isDeduplicate() === false ? $index : $key
                );

                $values = $field->getRequestValue() !== false ? $field->getRequestValue() : null;

                $apply = $field->apply(
                    static fn ($data): mixed => data_set($data, $field->getColumn(), $values),
                    $values,
                );

                $row = $applyValues[$index] ?? [];
                $row[$this->getRelatedKeyName()] = $key;

                data_set(
                    $row,
                    str_replace($this->getPivotAs() . '.', '', $field->getColumn()),
                    data_get($apply, $field->getColumn()),
                );
                /** @var array<string, mixed> $row */
                $applyValues[$index] = $row;
            }
        }

        $result = new Collection($applyValues)->mapWithKeys(function (array $value, int|string $index): array {
            /** @var int|string $key */
            $key = $value[$this->getRelatedKeyName()];

            return $this->isDeduplicate()
                ? [$key => \Illuminate\Support\Arr::except($value, $this->getRelatedKeyName())]
                : [$index => $value];
        });

        if (self::$silentApply) {
            data_set($item, $this->getRelationName(), $result->toArray());
        } elseif ($this->isDeduplicate() === false) {
            $this->getRelationFor($item)->sync([]);

            $result->each(fn (array $value) => $this->getRelationFor($item)->attach(
                $value[$this->getRelatedKeyName()],
                \Illuminate\Support\Arr::except($value, $this->getRelatedKeyName())
            ));
        } else {
            $this->getRelationFor($item)->sync($result);
        }

        return $item;
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        if ($this->isPivotModalMode()) {
            return $data;
        }

        $this->getFields()
            ->onlyFields()
            ->each(static fn (FieldContract $field): mixed => $field->beforeApply($data));

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        if (! $this->getResourceOrFail()->isDeleteRelationships()) {
            return $data;
        }

        $values = $this->toValue(withDefault: false);

        if (is_iterable($values) && filled($values)) {
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

    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): mixed
    {
        $casted = $this->getRelatedModel();
        /** @var Collection<array-key, int|string|null> $keys */
        $keys = Collection::wrap($value);
        $value = $keys
            ->map(fn (int|string|null $key): Model => clone ($this->makeRelatedModel($key, related: $this->getRelation()?->getRelated()) ?? throw \MoonShine\Laravel\Exceptions\ModelRelationFieldException::relationRequired()))
            ->values();

        $casted?->setRelation($this->getRelationName(), $value);
        $except[$this->getColumn()] = $this->getColumn();

        return $value;
    }

    public function getReactiveValue(): mixed
    {
        if ($this->isPivotModalMode()) {
            throw FieldException::reactivityNotSupported(static::class, 'with pivotModalMode');
        }

        if ($this->isAsyncSearch()) {
            throw FieldException::reactivityNotSupported(static::class, 'with asyncSearch');
        }

        return $this->getCollectionValue()->pluck($this->getRelatedKeyName());
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->asyncSettings([
            'selectedValuesKey' => 'value',
            'withAllFields' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        $viewData = [
            'isTreeMode' => $this->isTree(),
            'isPivotModalMode' => $this->isPivotModalMode(),
            'isHorizontalMode' => $this->isHorizontalMode(),
            'isSelectMode' => $this->isSelectMode(),
            'isDeduplicate' => $this->isDeduplicate(),
            'isAsyncSearch' => $this->isAsyncSearch(),
            'asyncSearchUrl' => $this->isAsyncSearch() ? $this->getAsyncSearchUrl() : '',
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
            'fragmentUrl' => $this->getFragmentUrl(),
            'relationName' => $this->getRelationName(),
        ];

        if ($this->isPivotModalMode() && $this->getRelated()?->getKey() === null) {
            $viewData['isCreatable'] = false;

            $this->withoutWrapper();

            return [
                ...$viewData,
                'component' => '',
            ];
        }

        if ($this->isPivotModalMode()) {
            return [
                ...$viewData,
                'component' => $this->getComponent(),
                'componentName' => $this->getComponent()->getName(),
            ];
        }

        $viewData['keys'] = $this->getKeys();

        if ($this->isSelectMode()) {
            $this->customAttributes(
                $this->getReactiveAttributes(),
            );

            return [
                ...$viewData,
                ...$this->getSelectViewData(),
                'values' => $this->getAvailableValues(),
            ];
        }

        if ($this->isTree()) {
            return [
                ...$viewData,
                'treeHtml' => $this->toTreeHtml(),
            ];
        }


        if ($this->isHorizontalMode()) {
            return [
                ...$viewData,
                'listHtml' => $this->toListHtml(),
            ];
        }

        return [
            ...$viewData,
            'component' => $this->getComponent(),
            'componentName' => $this->getComponent()->getName(),
            'buttons' => $this->getButtons(),
        ];
    }
}
