<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButton;
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

    /**
     * @throws Throwable
     */
    public function keyValue(
        string $key = 'Key',
        string $value = 'Value'
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;

        $this->fields([
            Text::make($key, 'key')
                ->customAttributes($this->attributes()->getAttributes()),

            Text::make($value, 'value')
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
        string $value = 'Value'
    ): static {
        $this->keyValue = false;
        $this->onlyValue = true;

        $this->fields([
            Text::make($value, 'value')
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

        if($this->isCreatable()) {
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
    public function asRelation(ModelResource $resource): self
    {
        $this->asRelation = true;
        $this->asRelationResource = $resource;

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
                ->customAttributes($this->removableAttributes ?: ['class' => 'btn-secondary'])
                ->showInLine();
        }

        return $buttons;
    }

    public function preparedFields(): Fields
    {
        return $this->getFields()->prepareAttributes()->map(function (Field $field): Field {
            throw_if(
                ! $this->isAsRelation() && $field instanceof ModelRelationField,
                new FieldException(
                    'Relationship fields in JSON field unavailable'
                )
            );

            $name = str($this->name());
            $level = $name->substrCount('$');

            if ($field instanceof Json) {
                $field->setLevel($level);
            }

            if ($field instanceof ID) {
                $field->beforeRender(fn (ID $id): View|string => $id->preview());
            }

            $name = $name
                ->append('[${index' . $level . '}]')
                ->append("[{$field->column()}]")
                ->replace('[]', '')
                ->when(
                    $field->getAttribute('multiple') || $field->isGroup(),
                    static fn (Stringable $str): Stringable => $str->append('[]')
                )->value();

            return $field
                ->setName($name)
                ->formName($this->getFormName())
                ->iterableAttributes($level)
                ->setParent($this);
        });
    }

    protected function resolvePreview(): View|string
    {
        if ($this->isRawMode()) {
            return parent::resolvePreview();
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
            ! $this->isPreviewMode() && $this->isCreatable(),
            static fn ($values): Collection => $values->push($emptyRow)
        )->when(
            ! $this->isPreviewMode() && ! $this->isCreatable() && blank($values),
            static fn ($values): Collection => $values->push($emptyRow)
        );

        $fields = $this->preparedFields();
        $sortable = ! $this->isPreviewMode()
            && ! $this->isAsRelation()
            && $this->isReorderable();

        if($sortable) {
            $fields->prepend(
                Preview::make(
                    formatted: static fn () => view('moonshine::ui.icons.heroicons.outline.bars-4')
                )->customAttributes(['class' => 'handle', 'style' => 'cursor: move'])
            );
        }

        return TableBuilder::make($fields, $values)
            ->name('json_' . $this->column())
            ->customAttributes(
                $this->attributes()
                    ->except('class')
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
        )->filter()->toArray();
    }

    protected function resolveOnApply(): ?Closure
    {
        if ($this->isAsRelation()) {
            return static fn ($item) => $item;
        }

        return function ($item) {
            $requestValues = array_filter($this->requestValue() ?: []);
            $applyValues = [];

            foreach ($requestValues as $index => $values) {
                foreach ($this->getFields() as $field) {
                    $field->appendRequestKeyPrefix(
                        "{$this->column()}.$index",
                        $this->requestKeyPrefix()
                    );

                    $apply = $field->apply(
                        fn ($data): mixed => data_set($data, $field->column(), $values[$field->column()] ?? ''),
                        $values
                    );

                    data_set(
                        $applyValues[$index],
                        $field->column(),
                        data_get($apply, $field->column())
                    );
                }
            }

            $preparedValues = $this->prepareOnApply($applyValues);
            $values = $this->isKeyValue() ? $preparedValues : array_values($preparedValues);

            return data_set(
                $item,
                str_replace('.', '->', $this->column()),
                $values
            );
        };
    }

    protected function resolveBeforeApply(mixed $data): mixed
    {
        $this->getFields()
            ->onlyFields()
            ->each(function (Field $field, $index) use ($data): void {
                $field->appendRequestKeyPrefix(
                    "{$this->column()}.$index",
                    $this->requestKeyPrefix()
                );

                $field->beforeApply($data);
            });

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        if ($this->isAsRelation()) {
            $requestValues = array_filter($this->requestValue() ?: []);
            $applyValues = [];

            foreach ($requestValues as $index => $values) {
                $values = $this->asRelationResource()
                    ?->getModel()
                    ?->forceFill($values) ?? $values;

                foreach ($this->getFields() as $field) {
                    $field->appendRequestKeyPrefix(
                        "{$this->column()}.$index",
                        $this->requestKeyPrefix()
                    );

                    $field->resolveFill($values->toArray(), $values);

                    $apply = $field->apply(
                        fn ($data): mixed => data_set($data, $field->column(), $values[$field->column()] ?? ''),
                        $values
                    );

                    data_set(
                        $applyValues[$index],
                        $field->column(),
                        data_get($apply, $field->column())
                    );
                }
            }

            $items = collect($this->prepareOnApply($applyValues));

            $ids = $items
                ->pluck($data->{$this->column()}()->getLocalKeyName())
                ->filter()
                ->toArray();

            $localKey = $data->{$this->column()}()->getLocalKeyName();

            $data->{$this->column()}()->when(
                ! empty($ids),
                fn (Builder $q) => $q->whereNotIn(
                    $localKey,
                    $ids
                )->delete()
            );

            $items->each(fn ($d) => $data->{$this->column()}()->updateOrCreate(
                [$localKey => $d[$localKey] ?? null],
                $d
            ));
        }

        $this->getFields()
            ->onlyFields()
            ->each(function (Field $field, $index) use ($data): void {
                $field->appendRequestKeyPrefix(
                    "{$this->column()}.$index",
                    $this->requestKeyPrefix()
                );

                $field->afterApply($data);
            });

        return $data;
    }

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
