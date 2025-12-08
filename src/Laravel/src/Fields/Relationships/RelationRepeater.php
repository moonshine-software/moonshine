<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\FieldWithComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeObject;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Contracts\RemovableContract;
use MoonShine\UI\Contracts\WrapperWithApplyContract;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Traits\Fields\HasVerticalMode;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Removable;
use MoonShine\UI\Traits\WithFields;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements FieldWithComponentContract<TableBuilderContract>
 */
class RelationRepeater extends ModelRelationField implements
    HasFieldsContract,
    FieldWithComponentContract,
    RemovableContract,
    HasDefaultValueContract,
    CanBeArray,
    CanBeObject
{
    use WithFields;
    use Removable;
    use WithDefaultValue;
    use HasVerticalMode;

    protected string $view = 'moonshine::fields.json';

    protected bool $isGroup = true;

    protected bool $resolveValueOnce = true;

    protected bool $isCreatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButtonContract $creatableButton = null;

    protected array $buttons = [];

    protected ?Closure $modifyTable = null;

    protected ?Closure $modifyRemoveButton = null;

    protected ?Closure $modifyCreateButton = null;

    protected bool $isReorderable = false;

    protected ?Closure $reorderableUrl = null;

    protected ?TableBuilderContract $resolvedComponent = null;

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
        $button = $this->creatableButton;

        if (! $button instanceof ActionButtonContract) {
            $button = ActionButton::make($this->getCore()->getTranslator()->get('moonshine::ui.add'))
                ->icon('plus-circle')
                ->customAttributes(['@click.prevent' => 'add()', 'class' => 'w-full']);
        }

        if (! \is_null($this->modifyCreateButton)) {
            $button = value($this->modifyCreateButton, $button, $this);
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

    /**
     * @param  Closure(static): string  $url
     */
    public function reorderable(Closure $url, Closure|bool|null $condition = null): static
    {
        $this->isReorderable = value($condition, $this) ?? true;
        $this->reorderableUrl = $url;

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->isReorderable;
    }

    /**
     * @param  Closure(TableBuilder $table, bool $preview): TableBuilder  $callback
     */
    public function modifyTable(Closure $callback): static
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
            $button = ActionButton::make()
                ->icon('trash')
                ->onClick(static fn ($action): string => 'remove', 'prevent')
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-error'])
                ->showInLine();

            if (! \is_null($this->modifyRemoveButton)) {
                $button = value($this->modifyRemoveButton, $button, $this);
            }

            $buttons[] = $button;
        }

        return $buttons;
    }

    /**
     * @throws Throwable
     */
    protected function prepareFields(): FieldsContract
    {
        $fields = $this->getFields();

        if (! $this->isPreviewMode()) {
            $fields->prepareAttributes();
        }

        $fields->onlyFields()->prepareReindexNames(parent: $this, before: function (self $parent, Field $field): void {
            if ($field instanceof HasUpdateOnPreviewContract && $field->isUpdateOnPreview()) {
                $field->nowOnResource($this->getResource());
            }

            $field
                ->disableSortable()
                ->withoutWrapper()
                ->setRequestKeyPrefix($parent->getRequestKeyPrefix())
            ;
        });

        return $fields;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     */
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
        $value = $this->isPreviewMode()
            ? $this->toFormattedValue()
            : $this->toValue();

        if ($this->getRelation() instanceof HasOne) {
            $this
                ->vertical()
                ->creatable(false);
        }

        $values = Collection::make(
            is_iterable($value)
                ? $value
                : [$value]
        );

        return $values->when(
            ! $this->isPreviewMode() && ! $this->isCreatable() && blank($values),
            static fn ($values): Collection => $values->push([null])
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveOldValue(mixed $old): mixed
    {
        foreach ($this->getFields() as $field) {
            if ($field instanceof File) {
                $column = $field->getColumn();

                $old = array_map(static fn (array $data): array => [
                    ...$data,
                    $column => $data[$field->getHiddenColumn()] ?? null,
                ], $old);
            }

            if ($field instanceof Json) {
                foreach ($old as $index => $value) {
                    $column = $field->getColumn();
                    $old[$index][$column] = $field->prepareOnApplyRecursive(
                        $value[$column] ?? []
                    );
                }
            }
        }

        return $old;
    }

    /**
     * @throws Throwable
     */
    public function getComponent(): ComponentContract
    {
        if (! \is_null($this->resolvedComponent)) {
            return $this->resolvedComponent;
        }

        $fields = $this->getPreparedFields();

        $reorderable = ! $this->isPreviewMode() && $this->isReorderable();
        $reorderableUrl = $reorderable ? \call_user_func($this->reorderableUrl, $this) : null;

        if ($reorderable) {
            $fields->prepend(
                Preview::make(
                    column: '__handle',
                    formatted: static fn () => Icon::make('bars-4'),
                )->customAttributes(['class' => 'handle', 'style' => 'cursor: move']),
            );
        }

        $component = TableBuilder::make($fields, $this->getValue())
            ->withoutKey()
            ->name("relation_repeater_{$this->getIdentity()}")
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
                static fn (TableBuilderContract $table): TableBuilderContract => $table->reorderable($reorderableUrl),
            )
            ->cast($this->getResource()?->getCaster())
            ->when(
                $this->isVertical(),
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
                fn (TableBuilder $tableBuilder) => value($this->modifyTable, $tableBuilder, $this->isPreviewMode())
            );

        if (! $this->isPreviewMode()) {
            $component = $component
                ->editable()
                ->reindex(prepared: true)
                ->when(
                    $this->isCreatable(),
                    fn (TableBuilderContract $table): TableBuilderContract => $table->creatable(
                        limit: $this->getCreateLimit(),
                        button: $this->getCreateButton()
                    )->removeAfterClone()
                )
                ->buttons($this->getButtons())
                ->simple();
        }

        return $this->resolvedComponent = $component;
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
                if (! $field->isCanApply()) {
                    continue;
                }

                $field->setNameIndex($index);

                $field->when($fill, fn (FieldContract $f): FieldContract => $f->fillCast(
                    $values,
                    $this->getResource()->getCaster()
                ));

                $apply = $callback($field, $values, $data);

                if ($field instanceof self) {
                    continue;
                }

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
        }

        $values = array_values($applyValues);

        return \is_null($response) ? data_set(
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
            response: fn (array $values, mixed $data): Model => $this->saveRelation($values, $data),
            fill: true,
        );
    }

    private function saveRelation(array $items, Model $model): Model
    {
        $collection = new Collection($items);

        if (self::$silentApply) {
            data_set($model, $this->getRelationName(), $collection);

            return $model;
        }

        $relationName = $this->getColumn();

        $related = $model->{$relationName}()->getRelated();

        $relatedKeyName = $related->getKeyName();
        $relatedQualifiedKeyName = $related->getQualifiedKeyName();

        $ids = $collection
            ->pluck($relatedKeyName)
            ->filter()
            ->toArray();

        $model->{$relationName}()->when(
            ! empty($ids),
            static fn (Builder $q) => $q->whereNotIn(
                $relatedQualifiedKeyName,
                $ids
            )->delete(),
            static fn (Builder $q) => $q->delete()
        );

        foreach ($collection as $item) {
            if (empty($item[$relatedKeyName])) {
                unset($item[$relatedKeyName]);
                $model->{$relationName}()->create($item);
            } else {
                $model->{$relationName}()->where($relatedKeyName, $item[$relatedKeyName])->update($item);
            }
        }

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

    public function isReactivitySupported(): bool
    {
        return false;
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
