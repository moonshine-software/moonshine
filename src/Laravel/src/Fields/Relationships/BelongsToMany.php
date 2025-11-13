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
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Crud\Contracts\Fields\HasAsyncSearchContract;
use MoonShine\Crud\Contracts\Fields\HasPivotContract;
use MoonShine\Crud\Contracts\Fields\HasRelatedValuesContact;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Traits\Fields\BelongsToOrManyCreatable;
use MoonShine\Laravel\Traits\Fields\HasHorizontalMode;
use MoonShine\Laravel\Traits\Fields\HasTreeMode;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\Laravel\Traits\Fields\WithRelatedLink;
use MoonShine\Laravel\Traits\Fields\WithRelatedValues;
use MoonShine\Support\Enums\Color;
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
 * @template-covariant R of \Illuminate\Database\Eloquent\Relations\BelongsToMany
 *
 * @extends ModelRelationField<R>
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<TableBuilderContract|ActionButtonContract>
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

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected array $translates = [
        'search' => 'moonshine::ui.search',
    ];

    protected bool $isGroup = true;

    protected bool $resolveValueOnce = true;

    protected string $treeParentColumn = '';

    protected bool $onlyCount = false;

    protected bool $inLine = false;

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

    protected array $buttons = [];

    protected ?string $columnLabel = null;

    protected ?TableBuilderContract $resolvedComponent = null;

    protected bool $isDeduplicate = true;

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

    protected function getPivotAs(): string
    {
        return $this->getRelation()?->getPivotAccessor() ?? 'pivot';
    }

    public function getTableComponentName(): string
    {
        return 'belongs_to_many_' . $this->getRelationName();
    }

    public function getRelatedKeyName(): string
    {
        return $this->getRelation()?->getRelated()?->getKeyName() ?? 'id';
    }

    public function getCollectionValue(): EloquentCollection
    {
        return new EloquentCollection($this->getValue() ?? []);
    }

    public function getSelectedValue(): string|array
    {
        $selected = $this->isValueWithModels()
            ? $this->getCollectionValue()->pluck($this->getRelatedKeyName())
            : $this->getCollectionValue();

        return $selected->toArray();
    }

    protected function isValueWithModels(mixed $data = null): bool
    {
        $data = new Collection($data ?? $this->toValue());

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

    protected function getResourceColumnLabel(): string
    {
        return $this->columnLabel ?? $this->getResource()->getTitle();
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
            $values = EloquentCollection::make($values);
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

        $values = $this->memoizeValues ?? ($this->isAsyncSearch() ? $this->toValue() : $this->resolveValuesQuery()->get());

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

            $checked = $this
                ->toValue()
                ->first(static fn ($item): bool => $item->getKey() === $value->getKey());

            return $value
                ->setRelations($checked?->getRelations() ?? $value->getRelations());
        });
    }

    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        $values = $this->getAvailableValues();

        if ($this->isRelatedLink()) {
            return $this->getRelatedLink();
        }

        $removeAfterClone = false;

        if (! $this->isPreviewMode() && $this->isAsyncSearch() && blank($values)) {
            $values->push($this->getResource()->getDataInstance());
            $removeAfterClone = true;
        }

        $titleColumn = $this->getResourceColumn();

        $checkedColumn = $this->getNameAttribute('${index0}');

        $identityField = Checkbox::make('#', $this->getRelatedKeyName())
            ->simpleMode()
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
            ->cast($this->getResource()->getCaster())
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

        $oldPivot = $this->getCore()->getRequest()->getOld($this->getPivotName());

        return Collection::make($old)
            ->map(fn (int|string|null $key): Model => clone $this->makeRelatedModel($key, relations: $oldPivot[$key] ?? [], related: $this->getRelation()?->getRelated()))
            ->values();
    }

    protected function resolveValue(): mixed
    {
        if (\is_array($this->toValue())) {
            $this->setValue(
                Collection::make($this->toValue())
                    ->map(fn (int|string|null $key): Model => clone $this->makeRelatedModel($key, related: $this->getRelation()?->getRelated()))
                    ->values()
            );
        }

        return parent::resolveValue();
    }

    protected function resolveRawValue(): mixed
    {
        return $this->getCollectionValue()
            ->map(static fn (Model $item) => $item->getKey())
            ->toJson();
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): Renderable|string
    {
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
                $value = $this->getColumnOrFormattedValue($item, data_get($item, $column, '') ?? null);

                if (! \is_null($this->inLineLink)) {
                    /** @var Link|string $linkValue */
                    $linkValue = \call_user_func($this->inLineLink, $item, $value, $this);

                    $value = $linkValue instanceof Link
                        ? $linkValue
                        : Link::make(
                            $linkValue,
                            $value,
                        );
                }

                /** @var Badge|bool $badgeValue */
                /** @phpstan-ignore-next-line  */
                $badgeValue = value($this->inLineBadge, $item, $value, $this);

                if ($badgeValue !== false) {
                    $badge = $badgeValue instanceof Badge
                        ? $badgeValue
                        : Badge::make((string) $value, Color::PRIMARY);

                    return $badge->customAttributes(['class' => 'm-1'])->render();
                }

                return $value;
            }, $this->inLineSeparator);
        }

        $fields = $this->getPreparedFields()
            ->prepend(Text::make($this->getResourceColumnLabel(), $column, $this->getFormattedValueCallback()))
            ->prepend(ID::make());

        return TableBuilder::make($fields, $values)
            ->preview()
            ->simple()
            ->cast($this->getResource()->getCaster())
            ->when(
                ! \is_null($this->modifyTable),
                fn (TableBuilderContract $tableBuilder) => value($this->modifyTable, $tableBuilder, false),
            )
            ->render();
    }

    public function getCheckedKeys(): Collection
    {
        $requestValues = new Collection($this->getRequestValue() ?: []);

        if ($this->isSelectMode() || $this->isTree() || $this->isHorizontalMode()) {
            return $requestValues;
        }

        if ($this->isDeduplicate() === false) {
            return $requestValues;
        }

        return $requestValues->keys();

    }

    public function getKeys(): array
    {
        if (\is_null($this->getValue())) {
            return [];
        }

        if ($this->isValueWithModels()) {
            return $this->getCollectionValue()->modelKeys();
        }

        return $this->getCollectionValue()->keys()->toArray();
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
        /* @var Model $item */
        $item = $data;

        $checkedKeys = $this->getCheckedKeys();
        $simpleSync = $this->isSelectMode() || $this->isTree() || $this->isHorizontalMode() || $this->getFields()->isEmpty();

        if ($simpleSync && self::$silentApply) {
            data_set($item, $this->getRelationName(), $checkedKeys);

            return $item;
        }

        if ($simpleSync && self::$silentApply === false) {
            $item->{$this->getRelationName()}()->sync($checkedKeys);

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

                $applyValues[$index][$this->getRelatedKeyName()] = $key;

                data_set(
                    /** @phpstan-ignore-next-line  */
                    $applyValues[$index],
                    str_replace($this->getPivotAs() . '.', '', $field->getColumn()),
                    data_get($apply, $field->getColumn()),
                );
            }
        }

        $result = (new Collection($applyValues))->mapWithKeys(fn (array $value, int $index): array => $this->isDeduplicate() ? [
            $value[$this->getRelatedKeyName()] => data_forget($value, $this->getRelatedKeyName()),
        ] : [$index => $value]);

        if (self::$silentApply) {
            data_set($item, $this->getRelationName(), $result->toArray());
        } elseif ($this->isDeduplicate() === false) {
            $item->{$this->getRelationName()}()->sync([]);

            $result->each(fn (array $value) => $item->{$this->getRelationName()}()->attach(
                $value[$this->getRelatedKeyName()],
                data_forget($value, $this->getRelatedKeyName())
            ));
        } else {
            $item->{$this->getRelationName()}()->sync($result);
        }

        return $item;
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(static fn (Field $field): mixed => $field->beforeApply($data));

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        if (! $this->getResource()->isDeleteRelationships()) {
            return $data;
        }

        $values = $this->toValue(withDefault: false);

        if (filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        static fn (Field $field): mixed => $field
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
        $value = Collection::make($value)
            ->map(fn (int|string|null $key): Model => clone $this->makeRelatedModel($key, related: $this->getRelation()?->getRelated()))
            ->values();

        $casted?->setRelation($this->getRelationName(), $value);
        $except[$this->getColumn()] = $this->getColumn();

        return $value;
    }

    public function getReactiveValue(): mixed
    {
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
            'isHorizontalMode' => $this->isHorizontalMode(),
            'isSelectMode' => $this->isSelectMode(),
            'isDeduplicate' => $this->isDeduplicate(),
            'isAsyncSearch' => $this->isAsyncSearch(),
            'asyncSearchUrl' => $this->isAsyncSearch() ? $this->getAsyncSearchUrl() : '',
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
            'fragmentUrl' => $this->getFragmentUrl(),
            'relationName' => $this->getRelationName(),
            'keys' => $this->getKeys(),
        ];

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
