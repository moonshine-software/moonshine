<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Buttons\BelongsToManyButton;
use MoonShine\Components\Badge;
use MoonShine\Components\Link;
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
use MoonShine\Resources\ModelResource;
use MoonShine\Support\Condition;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\HasTreeMode;
use MoonShine\Traits\Fields\OnlyLink;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithRelatedValues;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\WithFields;
use Throwable;

/**
 * @extends ModelRelationField<\Illuminate\Database\Eloquent\Relations\BelongsToMany>
 * @extends HasResource<ModelResource, ModelResource>
 */
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
    use OnlyLink;

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected bool $isGroup = true;

    protected string $treeParentColumn = '';

    protected bool $onlyCount = false;

    protected bool $inLine = false;

    protected ?Closure $inLineLink = null;

    protected string $inLineSeparator = '';

    protected Closure|bool $inLineBadge = false;

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

    public function inLine(string $separator = '', Closure|bool $badge = false, ?Closure $link = null): static
    {
        $this->inLine = true;
        $this->inLineSeparator = $separator;
        $this->inLineBadge = $badge;
        $this->inLineLink = $link;

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
        if (! $this->isCreatable()) {
            return null;
        }

        $button = BelongsToManyButton::for($this, button: $this->creatableButton);

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
            ActionButton::make('')
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

    protected function isValueWithModels(mixed $data = null): bool
    {
        $data = collect($data ?? $this->toValue());

        if ($data->isEmpty()) {
            return false;
        }

        return $data->every(fn ($item): bool => $item instanceof Model);
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
                    $field->nameFrom(
                        $this->getWrapName(),
                        $this->getPivotName(),
                        "\${index0}",
                        $this->getPivotAs(),
                        $field->column()
                    )
                )
                ->setParent($this)
                ->formName($this->getFormName())
                ->iterableAttributes()
        );
    }

    public function fragmentUrl(): string
    {
        return to_page(
            page: moonshineRequest()->getPage(),
            resource: moonshineRequest()->getResource(),
            params: ['resourceItem' => moonshineRequest()->getItemID()],
            fragment: $this->getRelationName()
        );
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        $values = parent::prepareFill($raw, $casted);

        // fix for filters
        if (blank($values) && filled($raw)) {
            $values = parent::prepareFill($raw);
        }

        if (! $values instanceof EloquentCollection) {
            $values = EloquentCollection::make($values);
        }

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
        // fix for filters
        if ($this->isAsyncSearch() && ! $this->isValueWithModels($this->memoizeValues) && filled($this->toValue())) {
            $this->memoizeValues = $this->getRelation()
                ?->getRelated()
                ?->newQuery()
                ?->findMany($this->toValue()) ?? EloquentCollection::make();

            $this->memoizeAllValues = $this->memoizeValues;
        }

        if ($this->isOnlyLink() && $this->isOnlyLinkOnForm()) {
            return $this->getOnlyLinkButton();
        }

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
            if (! $this->isValueWithModels()) {
                $data = $this->toValue();

                return $value
                    ->setRelations($value->getRelations())
                    ->setAttribute($checkedColumn, isset($data[$value->getKey()]) && $data[$value->getKey()]);
            }

            $checked = $this->toValue()
                ->first(fn ($item): bool => $item->getKey() === $value->getKey());

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
            ->reindex(prepared: true)
            ->withNotFound();
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'table' => $this->resolveValue(),
        ];
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
        $values = $this->toValue() ?? collect();
        $column = $this->getResourceColumn();

        if ($this->isRawMode()) {
            return $values
                ->map(fn (Model $item) => $item->getKey())
                ->toJson();
        }

        if ($this->isOnlyLink()) {
            return $this->getOnlyLinkButton(preview: true)->render();
        }

        if ($this->onlyCount) {
            return (string) $values->count();
        }

        if ($this->inLine) {
            return $values->implode(function (Model $item) use ($column) {
                $value = $this->columnOrFormattedValue($item, data_get($item, $column) ?? false);

                if (! is_null($this->inLineLink)) {
                    $linkValue = value($this->inLineLink, $item, $value, $this);

                    $value = $linkValue instanceof Link
                        ? $linkValue
                        : Link::make(
                            $linkValue,
                            $value,
                        );
                }

                $badgeValue = value($this->inLineBadge, $item, $value, $this);

                if ($badgeValue !== false) {
                    $badge = $badgeValue instanceof Badge
                        ? $badgeValue
                        : Badge::make((string) $value, 'primary');

                    return $badge->customAttributes(['class' => 'm-1'])->render();
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
            foreach ($this->preparedFields() as $field) {
                $field->setNameIndex($key);

                $values = request()->input("{$this->getPivotName()}.$key", []);

                $apply = $field->apply(
                    fn ($data): mixed => data_set($data, $field->column(), data_get($values, $field->column())),
                    $values
                );

                data_set(
                    $applyValues[$key],
                    str_replace($this->getPivotAs() . '.', '', $field->column()),
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
            ->each(function (Field $field) use ($data): void {
                $field->beforeApply($data);
            });

        return $data;
    }

    /**
     * @throws Throwable
     */
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
