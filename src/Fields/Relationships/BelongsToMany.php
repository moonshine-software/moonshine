<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Buttons\BelongsToManyButton;
use MoonShine\Components\Badge;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
use MoonShine\Fields\Preview;
use MoonShine\Fields\Text;
use MoonShine\Support\Condition;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\HasTreeMode;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithRelatedValues;
use MoonShine\Traits\WithFields;
use Throwable;

class BelongsToMany extends ModelRelationField implements
    HasRelatedValues,
    HasPivot,
    HasFields,
    HasAsyncSearch
{
    use WithFields;
    use WithRelatedValues;
    use Searchable;
    use WithAsyncSearch;
    use HasTreeMode;
    use HasPlaceholder;

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected bool $isGroup = true;

    protected string $treeParentColumn = '';

    protected bool $onlyCount = false;

    protected bool $inLine = false;

    protected string $inLineSeparator = '';

    protected bool $inLineBadge = false;

    protected bool $selectMode = false;

    protected bool $isCreatable = false;

    protected ?ActionButton $creatableButton = null;

    protected array $buttons = [];

    protected ?Collection $memoizeAllValues = null;

    protected ?string $columnLabel = null;

    public function getView(): string
    {
        if ($this->isTree()) {
            return 'moonshine::fields.shared.tree';
        }

        return parent::getView();
    }

    public function onlyCount(): static
    {
        $this->onlyCount = true;

        return $this;
    }

    public function inLine(string $separator = '', bool $badge = false): static
    {
        $this->inLine = true;
        $this->inLineSeparator = $separator;
        $this->inLineBadge = $badge;

        return $this;
    }

    public function selectMode(): self
    {
        $this->selectMode = true;

        return $this;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?ActionButton $button = null,
    ): static {
        $this->isCreatable = Condition::boolean($condition, true);
        $this->creatableButton = $button;

        return $this;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    /**
     * @throws Throwable
     */
    public function createButton(): ?ActionButton
    {
        if(! is_null($this->creatableButton)) {
            return $this->creatableButton;
        }

        if (! $this->isCreatable()) {
            return null;
        }

        $button = BelongsToManyButton::for($this);

        return $button->isSee($this->getRelatedModel())
            ? $button
            : null;
    }

    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function withCheckAll(): self
    {
        return $this->buttons([
            ActionButton::make('', '')
                ->onClick(fn (): string => 'checkAll', 'prevent')
                ->primary()
                ->icon('heroicons.outline.check'),
        ]);
    }

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons);
    }

    public function isSelectMode(): bool
    {
        return $this->selectMode;
    }

    protected function getPivotAs(): string
    {
        return $this->getRelation()?->getPivotAccessor() ?? 'pivot';
    }

    protected function getPivotName(): string
    {
        return "{$this->getRelationName()}_pivot";
    }

    public function getTableComponentName(): string
    {
        return 'belongs_to_many_' . $this->getRelationName();
    }

    public function selectedKeys(): Collection
    {
        return $this->isValueWithModels()
            ? collect($this->toValue())->pluck($this->getRelation()?->getRelated()?->getKeyName() ?? 'id')
            : collect($this->toValue());
    }

    protected function isValueWithModels(): bool
    {
        return collect($this->toValue())->every(fn ($item): bool => $item instanceof Model);
    }

    public function columnLabel(string $label): self
    {
        $this->columnLabel = $label;

        return $this;
    }

    protected function getResourceColumnLabel(): string
    {
        return $this->columnLabel ?? $this->getResource()->title();
    }

    public function preparedFields(): Fields
    {
        return $this->getFields()->prepareAttributes()->map(
            fn (Field $field): Field => (clone $field)
                ->setColumn("{$this->getPivotAs()}.{$field->column()}")
                ->setAttribute('class', 'pivotField')
                ->setName(
                    "{$this->getPivotName()}[\${index0}][{$field->column()}]"
                )
                ->setParent($this)
                ->formName($this->getFormName())
                ->iterableAttributes()
        );
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        // if filter data or raw fill logic
        if (is_array($values = $raw[$this->getRelationName()] ?? false) && $values !== []) {
            $values = array_filter($values);

            $keys = $this->isSelectMode()
                ? $values
                : array_keys($values);

            $related = $this->getRelation()?->getRelated();

            $casted->setRelation(
                $this->getRelationName(),
                $related?->query()
                    ->whereIn($related->getKeyName(), $keys)?->get() ?? []
            );
        }

        $values = parent::prepareFill($raw, $casted);

        if ($this->isAsyncSearch()) {
            $this->memoizeValues = $values;
            $this->memoizeAllValues = $values;
        }

        return $values;
    }

    /**
     * @throws Throwable
     */
    protected function resolveValue(): mixed
    {
        $titleColumn = $this->getResourceColumn();

        $checkedColumn = $this->name('${index0}');
        $identityField = Checkbox::make('#', $checkedColumn)
            ->setAttribute('class', 'pivotChecker')
            ->setName($checkedColumn)
            ->formName($this->getFormName())
            ->iterableAttributes();

        $fields = $this->preparedFields()
            ->prepend(
                Preview::make($this->getResourceColumnLabel(), $titleColumn, $this->formattedValueCallback())
                    ->formName($this->getFormName())
                    ->customAttributes(['class' => 'pivotTitle'])
            )
            ->prepend($identityField);

        $values = $this->memoizeAllValues ?? $this->resolveValuesQuery()->get();
        $this->memoizeAllValues = $values;

        $values = $values->map(function ($value) use ($checkedColumn) {
            if ($this->isValueWithModels()) {
                $checked = $this->toValue()
                    ->first(fn ($item): bool => $item->getKey() === $value->getKey());
            } else {
                $data = $this->toValue();

                return $value
                    ->setRelations($value->getRelations())
                    ->setAttribute($checkedColumn, isset($data[$value->getKey()]) && $data[$value->getKey()]);
            }

            return $value
                ->setRelations($checked?->getRelations() ?? $value->getRelations())
                ->setAttribute($checkedColumn, ! is_null($checked));
        });

        $removeAfterClone = false;

        if (! $this->isPreviewMode() && $this->isAsyncSearch() && blank($values)) {
            $values->push($this->getResource()->getModel());
            $removeAfterClone = true;
        }

        return TableBuilder::make(items: $values)
            ->name($this->getTableComponentName())
            ->customAttributes($this->attributes()->jsonSerialize())
            ->fields($fields)
            ->when(
                $removeAfterClone,
                fn (TableBuilder $table): TableBuilder => $table->customAttributes([
                    'data-remove-after-clone' => 1,
                ])
            )
            ->cast($this->getModelCast())
            ->trAttributes(
                fn (
                    Model $data,
                    int $row,
                    ComponentAttributeBag $attributes
                ): ComponentAttributeBag => $attributes->merge([
                    'data-key' => $data->getKey(),
                ])
            )
            ->preview()
            ->simple()
            ->editable()
            ->reindex()
            ->withNotFound();
    }

    protected function columnOrFormattedValue(Model $item, string|int $default): string|int
    {
        if (is_closure($this->formattedValueCallback())) {
            return value(
                $this->formattedValueCallback(),
                $item,
                $this
            );
        }

        return $default;
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): View|string
    {
        $values = $this->toValue() ?? [];
        $column = $this->getResourceColumn();

        if ($this->isRawMode()) {
            return $values
                ->map(fn (Model $item) => $item->getKey())
                ->toJson();
        }

        if ($this->onlyCount) {
            return (string) $values->count();
        }

        if ($this->inLine) {
            return $values->implode(function (Model $item) use ($column) {
                $value = $this->columnOrFormattedValue($item, data_get($item, $column) ?? false);

                if ($this->inLineBadge) {
                    return Badge::make($value, 'primary')
                        ->customAttributes(['class' => 'm-1'])
                        ->render();
                }

                return $value;
            }, $this->inLineSeparator) ?? '';
        }

        $fields = $this->preparedFields()
            ->prepend(Text::make($this->getResourceColumnLabel(), $column, $this->formattedValueCallback()))
            ->prepend(ID::make());

        return TableBuilder::make($fields, $values)
            ->preview()
            ->simple()
            ->cast($this->getModelCast())
            ->render();
    }

    protected function resolveOnApply(): ?Closure
    {
        return static fn ($item) => $item;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        /* @var Model $item */
        $item = $data;
        $requestValues = array_filter($this->requestValue() ?: []);
        $applyValues = [];

        if ($this->isSelectMode() || $this->isTree()) {
            $item->{$this->getRelationName()}()->sync($requestValues);

            return $data;
        }

        if ($this->getFields()->isEmpty()) {
            $item->{$this->getRelationName()}()->sync(
                array_keys($requestValues)
            );

            return $data;
        }

        foreach ($requestValues as $key => $checked) {
            foreach ($this->getFields() as $field) {
                $field->appendRequestKeyPrefix(
                    "{$this->getPivotName()}.$key",
                    $this->requestKeyPrefix()
                );

                $values = request($field->requestKeyPrefix());

                $apply = $field->apply(
                    fn ($data): mixed => data_set($data, $field->column(), $values[$field->column()] ?? null),
                    $values
                );

                data_set(
                    $applyValues[$key],
                    $field->column(),
                    data_get($apply, $field->column())
                );
            }
        }

        $item->{$this->getRelationName()}()->sync($applyValues);

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveBeforeApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(function (Field $field, $index) use ($data): void {
                $field->appendRequestKeyPrefix(
                    "{$this->getPivotName()}.$index",
                    $this->requestKeyPrefix()
                );

                $field->beforeApply($data);
            });

        return $data;
    }

    protected function resolveAfterDestroy(mixed $data): mixed
    {
        if (! $this->getResource()->deleteRelationships()) {
            return $data;
        }

        $values = $this->toValue(withDefault: false);

        if (filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        fn (Field $field): mixed => $field
                            ->resolveFill($value->toArray(), $value)
                            ->afterDestroy($value)
                    );
            }
        }

        return $data;
    }
}
