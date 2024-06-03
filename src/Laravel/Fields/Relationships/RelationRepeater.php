<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Fields\Relationships;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeObject;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Contracts\Fields\RemovableContract;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Removable;
use MoonShine\UI\Traits\WithFields;
use Throwable;

class RelationRepeater extends ModelRelationField implements
    HasFields,
    RemovableContract,
    HasDefaultValue,
    DefaultCanBeArray,
    DefaultCanBeObject
{
    use WithFields;
    use Removable;
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.json';

    protected bool $isGroup = true;

    protected bool $isVertical = false;

    protected bool $isCreatable = true;

    protected ?int $creatableLimit = null;

    protected ?ActionButton $creatableButton = null;

    protected array $buttons = [];

    protected bool $deleteWhenEmpty = false;

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

    public function vertical(Closure|bool|null $condition = null): self
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
        ?ActionButton $button = null
    ): self {
        $this->isCreatable = value($condition, $this) ?? true;

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
                ->icon('trash')
                ->onClick(fn ($action): string => 'remove', 'prevent')
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-error'])
                ->showInLine();
        }

        return $buttons;
    }

    public function preparedFields(): Fields
    {
        return $this->getFields()->prepareAttributes()->prepareReindex(parent: $this, before: function (self $parent, Field $field): void {
            $field->withoutWrapper();
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
        $emptyRow = $this->getResource()?->getModel();

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

        return TableBuilder::make($fields, $values)
            ->name('relation_repeater_' . $this->getColumn())
            ->customAttributes(
                $this->attributes()
                    ->except(['class', 'data-name', 'data-column'])
                    ->jsonSerialize()
            )
            ->cast($this->getResource()?->getModelCast())
            ->when(
                $this->isVertical(),
                fn (TableBuilder $table): TableBuilder => $table->vertical()
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
                ?->getModel()
                ?->forceFill($values) ?? $values;

            $requestValues[$index] = $values;

            foreach ($this->getFields()->onlyFields() as $field) {
                $field->appendRequestKeyPrefix(
                    "{$this->getColumn()}.$index",
                    $this->requestKeyPrefix()
                );

                $field->when($fill, fn (Field $f): Field => $f->resolveFill($values->toArray(), $values));

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
            callback: fn (Field $field, mixed $values): mixed => $field->apply(
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
            callback: fn (Field $field, mixed $values): mixed => $field->beforeApply($values),
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
            callback: fn (Field $field, mixed $values): mixed => $field->apply(
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
            fn (Builder $q) => $q->whereNotIn(
                $relatedQualifiedKeyName,
                $ids
            )->delete()
        );

        $model->{$relationName}()->when(
            empty($ids) && $this->deleteWhenEmpty,
            fn (Builder $q) => $q->delete()
        );

        $items->each(fn ($item) => $model->{$relationName}()->updateOrCreate(
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
        if (! $this->getResource()?->deleteRelationships()) {
            return $data;
        }

        $values = $this->toValue(withDefault: false);

        if (filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        fn (Field $field): mixed => $field
                            ->when(
                                $value instanceof Arrayable,
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

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'component' => $this->resolveValue()
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
}
