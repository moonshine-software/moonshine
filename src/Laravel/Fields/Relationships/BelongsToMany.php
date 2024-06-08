<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Laravel\Buttons\BelongsToManyButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Contracts\Fields\HasAsyncSearch;
use MoonShine\Laravel\Contracts\Fields\HasPivot;
use MoonShine\Laravel\Contracts\Fields\HasRelatedValues;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Fields\HasTreeMode;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\Laravel\Traits\Fields\WithRelatedValues;
use MoonShine\Support\Traits\HasResource;
use MoonShine\UI\Collections\ActionButtons;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\WithFields;
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

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected bool $isGroup = true;

    protected string $treeParentColumn = '';

    protected bool $onlyCount = false;

    protected bool $inLine = false;

    protected ?Closure $inLineLink = null;

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

    public function inLine(string $separator = '', bool $badge = false, ?Closure $link = null): static
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
        $this->isCreatable = value($condition, $this) ?? true;
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
                ->icon('check'),
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

    public function resolveSelectedValue(): string|array
    {
        $selected = $this->isValueWithModels()
            ? collect($this->toValue())->pluck($this->getRelation()?->getRelated()?->getKeyName() ?? 'id')
            : collect($this->toValue());

        return $selected->toArray();
    }

    protected function isValueWithModels(): bool
    {
        $data = collect($this->toValue());

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

    public function preparedFields(): FieldsCollection
    {
        // TODO(3.0) use prepareReindex
        return $this->getFields()->prepareAttributes()->map(
            fn (Field $field): Field => (clone $field)
                ->setColumn("{$this->getPivotAs()}.{$field->getColumn()}")
                ->setAttribute('class', 'pivotField')
                ->setNameAttribute(
                    "{$this->getPivotName()}[\${index0}][{$field->getColumn()}]"
                )
                ->setParent($this)
                ->formName($this->getFormName())
                ->iterableAttributes()
                ->withoutWrapper()
        );
    }

    public function fragmentUrl(): string
    {
        return toPage(
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
        $titleColumn = $this->getResourceColumn();

        $checkedColumn = $this->getNameAttribute('${index0}');
        $identityField = Checkbox::make('#', $checkedColumn)
            ->withoutWrapper()
            ->setAttribute('class', 'pivotChecker')
            ->setNameAttribute($checkedColumn)
            ->formName($this->getFormName())
            ->iterableAttributes();

        $fields = $this->preparedFields()
            ->prepend(
                Preview::make($this->getResourceColumnLabel(), $titleColumn, $this->formattedValueCallback())
                    ->withoutWrapper()
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
            ->cast($this->getResource()->getModelCast())
            ->simple()
            ->editable()
            ->reindex(prepared: true)
            ->withNotFound();
    }

    protected function columnOrFormattedValue(Model $item, string|int $default): string|int
    {
        if (! is_null($this->formattedValueCallback())) {
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

                if ($this->inLineBadge) {
                    return Badge::make((string) $value, 'primary')
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
            ->cast($this->getResource()->getModelCast())
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
        $requestValues = array_filter($this->getRequestValue() ?: []);
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
                    fn ($data): mixed => data_set($data, $field->getColumn(), $values[$field->getColumn()] ?? null),
                    $values
                );

                data_set(
                    $applyValues[$key],
                    $field->getColumn(),
                    data_get($apply, $field->getColumn())
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

    public function getKeys(): array
    {
        if(is_null($this->toValue())) {
            return [];
        }

        if($this->isValueWithModels()) {
            return $this->toValue()?->modelKeys();
        }

        return $this->toValue()->keys()->toArray();
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'component' => $this->resolveValue(),
            'buttons' => $this->getButtons(),
            'values' => $this->getRelation() ? $this->getValues()->toArray() : [],
            'isSearchable' => $this->isSearchable(),
            'isAsyncSearch' => $this->isAsyncSearch(),
            'isSelectMode' => $this->isSelectMode(),
            'asyncSearchUrl' => $this->asyncSearchUrl(),
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->createButton(),
            'fragmentUrl' => $this->fragmentUrl(),
            'relationName' => $this->getRelationName(),
            'keys' => $this->getKeys(),
        ];
    }
}
