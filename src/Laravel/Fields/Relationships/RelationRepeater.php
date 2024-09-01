<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\RemovableContract;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Removable;
use MoonShine\UI\Traits\WithFields;
use Throwable;

class RelationRepeater extends ModelRelationField implements
    HasFieldsContract,
    RemovableContract,
    HasDefaultValueContract,
    CanBeArray,
    CanBeObject
{
    /** @use WithFields<Fields> */
    use WithFields;
    use Removable;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.json';

    protected bool $isGroup = true;

    protected bool $hasOld = false;

    protected bool $resolveValueOnce = true;

    protected bool $isVertical = false;

    protected bool $isCreatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButtonContract $creatableButton = null;

    protected array $buttons = [];

    protected bool $deleteWhenEmpty = false;

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyCreateButton = null;

    protected ?Closure $modifyRemoveButton = null;

    public function __construct(
        string|Closure $label,
        ?string $relationName = null,
        string|Closure|null $formatted = null,
        ModelResource|string|null $resource = null
    ) {
        parent::__construct($label, $relationName, $formatted, $resource);

        $this->fields(
            $this->getResource()?->getFormFields()?->onlyFields() ?? []
        );
    }

    public function vertical(Closure|bool|null $condition = null): static
    {
        $this->isVertical = value($condition, $this) ?? true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->isVertical;
    }

    public function creatable(
        Closure|bool|null $condition = null,
        ?int $limit = null,
        ?ActionButtonContract $button = null
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
        if (! is_null($this->modifyCreateButton)) {
            return value($this->creatableButton, $this->modifyCreateButton, $this);
        }

        return $this->creatableButton;
    }

    public function isCreatable(): bool
    {
        return $this->isCreatable;
    }

    public function getCreateLimit(): ?int
    {
        return $this->creatableLimit;
    }

    /**
     * @param  Closure(TableBuilder $table, bool $preview, self $field): TableBuilder  $callback
     */
    public function modifyTable(Closure $callback): self
    {
        $this->modifyTable = $callback;

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
     * @param  Closure(ActionButton $button, self $field): ActionButton  $callback
     */
    public function modifyRemoveButton(Closure $callback): self
    {
        $this->modifyRemoveButton = $callback;

        return $this;
    }

    public function buttons(array $buttons): static
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
            $button = ActionButton::make('', '#')
                ->icon('trash')
                ->onClick(static fn ($action): string => 'remove', 'prevent')
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-error'])
                ->showInLine();

            if (! is_null($this->modifyRemoveButton)) {
                $button = value($this->modifyRemoveButton, $button, $this);
            }

            $buttons[] = $button;
        }

        return $buttons;
    }

    protected function prepareFields(): FieldsContract
    {
        return $this->getFields()->prepareAttributes()->prepareReindexNames(parent: $this, before: static function (self $parent, Field $field): void {
            $field
                ->disableSortable()
                ->withoutWrapper()
                ->setRequestKeyPrefix($parent->getRequestKeyPrefix())
            ;
        });
    }

    protected function resolvePreview(): string|Renderable
    {
        return $this
            ->getComponent()
            ->simple()
            ->preview()
            ->render();
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
        $emptyRow = $this->getResource()?->getDataInstance();

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

        return collect($values)->when(
            ! $this->isPreviewMode() && ! $this->isCreatable() && blank($values),
            static fn ($values): Collection => $values->push($emptyRow)
        );
    }

    /**
     * @throws Throwable
     */
    protected function getComponent(): RenderableContract
    {
        $fields = $this->getPreparedFields();

        return TableBuilder::make($fields, $this->getValue())
            ->name("relation_repeater_{$this->getIdentity()}")
            ->inside('field')
            ->customAttributes(
                $this->getAttributes()
                    ->except(['class', 'data-name', 'data-column'])
                    ->jsonSerialize()
            )
            ->cast($this->getResource()?->getCaster())
            ->when(
                $this->isVertical(),
                static fn (TableBuilderContract $table): TableBuilderContract => $table->vertical()
            )
            ->when(
                ! is_null($this->modifyTable),
                fn (TableBuilder $tableBuilder) => value($this->modifyTable, $tableBuilder, preview: $this->isPreviewMode())
            );
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
        $requestValues = array_filter($this->getRequestValue() ?: []);

        $applyValues = [];

        foreach ($requestValues as $index => $values) {
            $values = $this->getResource()
                ?->getDataInstance()
                ?->forceFill($values) ?? $values;

            $requestValues[$index] = $values;

            foreach ($this->resetPreparedFields()->getPreparedFields() as $field) {
                $field->setNameIndex($index);

                $field->when($fill, fn (FieldContract $f): FieldContract => $f->fillCast(
                    $values,
                    $this->getResource()->getCaster()
                ));

                $apply = $callback($field, $values, $data);

                data_set(
                    $applyValues[$index],
                    $field->getColumn(),
                    data_get($apply, $field->getColumn())
                );
            }
        }

        $values = array_values($applyValues);

        return is_null($response) ? data_set(
            $data,
            str_replace('.', '->', $this->getColumn()),
            $values
        ) : $response($values, $data);
    }

    protected function resolveOnApply(): ?Closure
    {
        return fn ($item): mixed => $this->resolveAppliesCallback(
            data: $item,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->apply(
                static fn ($data): mixed => data_set($data, $field->getColumn(), $values[$field->getColumn()] ?? ''),
                $values
            ),
            response: static fn (array $values, mixed $data): mixed => $data
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
            response:  static fn (array $values, mixed $data): mixed => $data
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: static fn (FieldContract $field, mixed $values): mixed => $field->apply(
                static fn ($data): mixed => data_set($data, $field->getColumn(), $values[$field->getColumn()] ?? ''),
                $values
            ),
            response: fn (array $values, mixed $data) => $this->saveRelation($values, $data),
            fill: true,
        );
    }

    private function saveRelation(array $items, mixed $model)
    {
        $items = collect($items);

        $relationName = $this->getColumn();

        $related = $model->{$relationName}()->getRelated();

        $relatedKeyName = $related->getKeyName();
        $relatedQualifiedKeyName = $related->getQualifiedKeyName();

        $ids = $items
            ->pluck($relatedKeyName)
            ->filter()
            ->toArray();

        $model->{$relationName}()->when(
            ! empty($ids),
            static fn (Builder $q) => $q->whereNotIn(
                $relatedQualifiedKeyName,
                $ids
            )->delete()
        );

        $model->{$relationName}()->when(
            empty($ids) && $this->deleteWhenEmpty,
            static fn (Builder $q) => $q->delete()
        );

        $items->each(static fn ($item) => $model->{$relationName}()->updateOrCreate(
            [$relatedQualifiedKeyName => $item[$relatedKeyName] ?? null],
            $item
        ));

        return $model;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        if (! $this->getResource()?->isDeleteRelationships()) {
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

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'component' => $this->getComponent()
                ->editable()
                ->reindex(prepared: true)
                ->when(
                    $this->isCreatable(),
                    fn (TableBuilderContract $table): TableBuilderContract => $table->creatable(
                        limit: $this->getCreateLimit(),
                        button: $this->getCreateButton()
                    )
                )
                ->buttons($this->getButtons())
                ->simple(),
        ];
    }
}
