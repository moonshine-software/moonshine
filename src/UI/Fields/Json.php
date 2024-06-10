<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Icon;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Contracts\Fields\HasFields;
use MoonShine\UI\Contracts\Fields\RemovableContract;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Removable;
use MoonShine\UI\Traits\WithFields;
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

    protected bool $isReorderable = true;

    protected bool $isFilterMode = false;

    /**
     * @throws Throwable
     */
    public function keyValue(
        string $key = 'Key',
        string $value = 'Value',
        ?Field $keyField = null,
        ?Field $valueField = null,
    ): static {
        $this->keyValue = true;
        $this->onlyValue = false;

        $this->fields([
            ($keyField ?? Text::make($key, 'key'))
                ->setColumn('key')
                ->customAttributes($this->attributes()->getAttributes()),

            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
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
        string $value = 'Value',
        ?Field $valueField = null,
    ): static {
        $this->keyValue = false;
        $this->onlyValue = true;

        $this->fields([
            ($valueField ?? Text::make($value, 'value'))
                ->setColumn('value')
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
        $this->isReorderable = value($condition, $this) ?? true;

        return $this;
    }

    public function isReorderable(): bool
    {
        return $this->isReorderable;
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

    public function preparedFields(): FieldsCollection
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
        $emptyRow = [null];

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
        $reorderable = ! $this->isPreviewMode()
            && $this->isReorderable();

        if ($reorderable) {
            $fields->prepend(
                Preview::make(
                    formatted: static fn () => Icon::make('bars-4')
                )->customAttributes(['class' => 'handle', 'style' => 'cursor: move'])
            );
        }

        return TableBuilder::make($fields, $values)
            ->name('repeater_' . $this->getColumn())
            ->customAttributes(
                $this->attributes()
                    ->except(['class', 'data-name', 'data-column'])
                    ->when(
                        $reorderable,
                        fn (MoonShineComponentAttributeBag $attr): MoonShineComponentAttributeBag => $attr->merge([
                            'data-handle' => '.handle',
                        ])
                    )
                    ->jsonSerialize()
            )
            ->when(
                $reorderable,
                fn (TableBuilder $table): TableBuilder => $table->reorderable()
            )
            ->when(
                $this->isVertical(),
                fn (TableBuilder $table): TableBuilder => $table->vertical()
            );
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
        )->filter(fn ($value): bool => $this->filterEmpty($value))->toArray();
    }

    private function filterEmpty(mixed $value): bool
    {
        if (is_iterable($value) && filled($value)) {
            return collect($value)
                ->filter(fn ($v): bool => $this->filterEmpty($v))
                ->isNotEmpty();
        }

        return ! blank($value);
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
            foreach ($this->getFields()->onlyFields() as $field) {
                $field->appendRequestKeyPrefix(
                    "{$this->getColumn()}.$index",
                    $this->requestKeyPrefix()
                );

                $field->when($fill, fn (Field $f): Field => $f->fillData($values));

                $apply = $callback($field, $values, $data);

                data_set(
                    $applyValues[$index],
                    $field->getColumn(),
                    data_get($apply, $field->getColumn())
                );
            }
        }

        $preparedValues = $this->prepareOnApply($applyValues);
        $values = $this->isKeyValue() ? $preparedValues : array_values($preparedValues);

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
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterApply(mixed $data): mixed
    {
        return $this->resolveAppliesCallback(
            data: $data,
            callback: fn (Field $field, mixed $values): mixed => $field->afterApply($values),
            response: static fn (array $values, mixed $data): mixed => $data,
        );
    }

    /**
     * @throws Throwable
     */
    protected function resolveAfterDestroy(mixed $data): mixed
    {
        $values = $this->toValue(withDefault: false);

        if (! $this->isKeyOrOnlyValue() && filled($values)) {
            foreach ($values as $value) {
                $this->getFields()
                    ->onlyFields()
                    ->each(
                        fn (Field $field): mixed => $field
                            ->fillData($value)
                            ->afterDestroy($value)
                    );
            }
        }

        return $data;
    }
}
