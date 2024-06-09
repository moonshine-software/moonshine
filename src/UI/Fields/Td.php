<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\UI\Components\FieldsGroup;
use MoonShine\UI\Components\Layout\LineBreak;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @method static static make(Closure|string $label, ?Closure $fields = null)
 */
class Td extends Template
{
    private ?Closure $conditionalFields = null;

    private ?Closure $tdAttributes = null;

    protected bool $withWrapper = false;

    protected bool $withLabels = false;

    public function __construct(Closure|string $label, ?Closure $fields = null)
    {
        parent::__construct($label);

        $this->conditionalFields($fields);
        $this->forcePreview();
    }

    public function withLabels(): static
    {
        $this->withLabels = true;

        return $this;
    }

    public function hasLabels(): bool
    {
        return $this->withLabels;
    }

    /**
     * @param  ?Closure(mixed $data, self $td): self $fields
     */
    public function conditionalFields(?Closure $fields = null): self
    {
        $this->conditionalFields = $fields;

        return $this;
    }

    public function hasConditionalFields(): bool
    {
        return ! is_null($this->conditionalFields);
    }

    public function getConditionalFields(): array
    {
        return value($this->conditionalFields, $this->getData()?->getOriginal(), $this);
    }

    protected function resolveFill(
        array $raw = [],
        ?CastedData $casted = null,
        int $index = 0
    ): static {
        return $this
            ->setRawValue($raw)
            ->setData($casted)
            ->setRowIndex($index);
    }

    /**
     * @param  Closure(mixed $data, self $td): array  $attributes
     */
    public function tdAttributes(Closure $attributes): self
    {
        $this->tdAttributes = $attributes;

        return $this;
    }

    public function hasTdAttributes(): bool
    {
        return ! is_null($this->tdAttributes);
    }

    public function resolveTdAttributes(mixed $data): array
    {
        return $this->hasTdAttributes()
            ? value($this->tdAttributes, $data, $this)
            : [];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    protected function resolvePreview(): string|View
    {
        $fields = $this->hasConditionalFields()
            ? $this->getConditionalFields()
            : $this->getFields();

        return FieldsGroup::make(fieldsCollection($fields))
            ->mapFields(fn (Field $field): Field => $field
                ->fillData($this->getData())
                ->beforeRender(fn (): string => $this->hasLabels() ? '' : (string) LineBreak::make())
                ->withoutWrapper($this->hasLabels())
                ->forcePreview())
            ->render();
    }

    public function render(): string
    {
        return $this->preview();
    }
}
