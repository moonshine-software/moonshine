<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Laravel\Buttons\BelongsToManyButton;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Contracts\Fields\HasAsyncSearch;
use MoonShine\Laravel\Contracts\Fields\HasPivot;
use MoonShine\Laravel\Contracts\Fields\HasRelatedValues;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Traits\Fields\HasTreeMode;
use MoonShine\Laravel\Traits\Fields\WithAsyncSearch;
use MoonShine\Laravel\Traits\Fields\WithRelatedLink;
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
use MoonShine\UI\Fields\FormElement;
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
    /** @use WithFields<Fields> */
    use WithFields;
    use WithRelatedValues;
    use Searchable;
    use WithAsyncSearch;
    use HasTreeMode;
    use HasPlaceholder;
    use WithRelatedLink;

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected bool $isGroup = true;

    protected bool $hasOld = false;

    protected bool $resolveValueOnce = true;

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

    protected ?string $columnLabel = null;

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

    public function selectMode(): static
    {
        $this->selectMode = true;

        return $this;
    }

    public function isSelectMode(): bool
    {
        return $this->selectMode;
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
    public function getCreateButton(): ?ActionButton
    {
        if (! $this->isCreatable()) {
            return null;
        }

        $button = BelongsToManyButton::for($this, button: $this->creatableButton);

        return $button->isSee($this->getRelatedModel())
            ? $button
            : null;
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
        ]);
    }

    public function getButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons);
    }

    protected function getPivotAs(): string
    {
        return $this->getRelation()?->getPivotAccessor() ?? 'pivot';
    }

    public function getTableComponentName(): string
    {
        return 'belongs_to_many_' . $this->getRelationName();
    }

    public function getSelectedValue(): string|array
    {
        $selected = $this->isValueWithModels()
            ? collect($this->toValue())->pluck($this->getRelation()?->getRelated()?->getKeyName() ?? 'id')
            : collect($this->toValue());

        return $selected->toArray();
    }

    protected function isValueWithModels(mixed $data = null): bool
    {
        $data = collect($data ?? $this->toValue());

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

    public function getPreparedFields(): FieldsCollection
    {
        return $this->getFields()->prepareAttributes()->prepareReindex(
            parent: $this,
            before: fn (self $parent, Field $field): FormElement => (clone $field)
                ->setColumn("{$this->getPivotAs()}.{$field->getColumn()}")
                ->setNameAttribute($field->getColumn())
                ->class('js-pivot-field')
                ->withoutWrapper(),
            performName: fn (string $name): string => str_replace("{$this->getPivotAs()}.", '', $name)
        );
    }

    public function getFragmentUrl(): string
    {
        return toPage(
            page: moonshineRequest()->getPage(),
            resource: moonshineRequest()->getResource(),
            params: ['resourceItem' => moonshineRequest()->getItemID()],
            fragment: $this->getRelationName()
        );
    }

    public function getCheckboxKey(): string
    {
        return '_checked';
    }

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        $values = parent::prepareFill($raw, $casted);

        if (! $values instanceof EloquentCollection) {
            $values = EloquentCollection::make($values);
        }

        return $values;
    }

    /**
     * @throws Throwable
     */
    protected function resolveValue(): mixed
    {
        if($this->isRelatedLink()) {
            return $this->getRelatedLink();
        }

        // fix for filters
        if ($this->isAsyncSearch() && ! $this->isValueWithModels($this->memoizeValues) && filled($this->toValue())) {
            $this->memoizeValues = $this->getRelation()
                ?->getRelated()
                ?->newQuery()
                ?->findMany($this->toValue()) ?? EloquentCollection::make();
        }

        if($this->isSelectMode()) {
            return $this->getValues()->toArray();
        }

        if($this->isTree()) {
            return $this->getKeys();
        }

        return ($this->memoizeValues ?? $this->resolveValuesQuery()->get())->map(function ($value) {
            if (! $this->isValueWithModels()) {
                $data = $this->toValue();

                return $value
                    ->setRelations([
                        $this->getPivotAs() => $data[$value->getKey()] ?? [],
                    ])
                    ->setAttribute($this->getCheckboxKey(), $data[$value->getKey()][$this->getCheckboxKey()] ?? false);
            }

            $checked = $this->toValue()
                ->first(static fn ($item): bool => $item->getKey() === $value->getKey());

            return $value
                ->setRelations($checked?->getRelations() ?? $value->getRelations())
                ->setAttribute($this->getCheckboxKey(), ! is_null($checked));
        });
    }

    protected function getTableValue(): TableBuilder
    {
        $values = $this->getValue();

        $removeAfterClone = false;

        if (! $this->isPreviewMode() && $this->isAsyncSearch() && blank($values)) {
            $values->push($this->getResource()->getModel());
            $removeAfterClone = true;
        }

        $titleColumn = $this->getResourceColumn();

        $checkedColumn = $this->getNameAttribute('${index0}');

        $identityField = Checkbox::make('#', $this->getCheckboxKey())
            ->withoutWrapper()
            ->class('js-pivot-checker')
            ->setNameAttribute($checkedColumn . "[{$this->getCheckboxKey()}]")
            ->formName($this->getFormName())
            ->iterableAttributes();

        $fields = $this->getPreparedFields()
            ->prepend(
                Preview::make($this->getResourceColumnLabel(), $titleColumn, $this->getFormattedValueCallback())
                    ->withoutWrapper()
                    ->formName($this->getFormName())
                    ->class('js-pivot-title')
            )
            ->prepend($identityField);

        return TableBuilder::make(items: $values)
            ->name($this->getTableComponentName())
            ->customAttributes($this->getAttributes()->jsonSerialize())
            ->fields($fields)
            ->when(
                $removeAfterClone,
                static fn (TableBuilder $table): TableBuilder => $table->customAttributes([
                    'data-remove-after-clone' => 1,
                ])
            )
            ->cast($this->getResource()->getModelCast())
            ->simple()
            ->editable()
            ->reindex(prepared: true)
            ->withNotFound();
    }

    protected function getColumnOrFormattedValue(Model $item, string|int $default): string|int
    {
        if (! is_null($this->getFormattedValueCallback())) {
            return value(
                $this->getFormattedValueCallback(),
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
                ->map(static fn (Model $item) => $item->getKey())
                ->toJson();
        }

        if ($this->isRelatedLink()) {
            return (string) $this->getRelatedLink(preview: true);
        }

        if ($this->onlyCount) {
            return (string) $values->count();
        }

        if ($this->inLine) {
            return $values->implode(function (Model $item) use ($column) {
                $value = $this->getColumnOrFormattedValue($item, data_get($item, $column) ?? false);

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
                        ->class('m-1')
                        ->render();
                }

                return $value;
            }, $this->inLineSeparator) ?? '';
        }

        $fields = $this->getPreparedFields()
            ->prepend(Text::make($this->getResourceColumnLabel(), $column, $this->getFormattedValueCallback()))
            ->prepend(ID::make());

        return TableBuilder::make($fields, $values)
            ->preview()
            ->simple()
            ->cast($this->getResource()->getModelCast())
            ->render();
    }

    public function getCheckedKeys(): Collection
    {
        $requestValues = collect($this->getRequestValue() ?: []);

        if($this->isSelectMode() || $this->isTree()) {
            return $requestValues;
        }

        return $requestValues
            ->filter(static fn (array $value) => $value['_checked'])
            ->keys();

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

        $checkedKeys = $this->getCheckedKeys();

        if ($this->isSelectMode() || $this->isTree() || $this->getFields()->isEmpty()) {
            $item->{$this->getRelationName()}()->sync($checkedKeys);

            return $data;
        }

        $applyValues = [];

        foreach ($checkedKeys as $key) {
            foreach ($this->getFields() as $field) {
                $field->appendRequestKeyPrefix(
                    "{$this->getRelationName()}.$key",
                    $this->getRequestKeyPrefix()
                );

                $values = request()->input($field->getRequestKeyPrefix());

                $apply = $field->apply(
                    static fn ($data): mixed => data_set($data, $field->getColumn(), $values[$field->getColumn()] ?? null),
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
                    "{$this->getRelationName()}.$index",
                    $this->getRequestKeyPrefix()
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
                            ->afterDestroy($value)
                    );
            }
        }

        return $data;
    }

    public function getKeys(): array
    {
        if (is_null($this->toValue())) {
            return [];
        }

        if ($this->isValueWithModels()) {
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
        $viewData = [
            'isTreeMode' => $this->isTree(),
            'isSelectMode' => $this->isSelectMode(),
            'isAsyncSearch' => $this->isAsyncSearch(),
            'asyncSearchUrl' => $this->getAsyncSearchUrl(),
            'isCreatable' => $this->isCreatable(),
            'createButton' => $this->getCreateButton(),
            'fragmentUrl' => $this->getFragmentUrl(),
            'relationName' => $this->getRelationName(),
        ];

        if($this->isSelectMode()) {
            return [
                ...$viewData,
                'isSearchable' => $this->isSearchable(),
            ];
        }

        if($this->isTree()) {
            return [
                ...$viewData,
                'treeHtml' => $this->toTreeHtml(),
            ];
        }

        return [
            ...$viewData,
            'component' => $this->getTableValue(),
            'buttons' => $this->getButtons(),
        ];
    }
}
