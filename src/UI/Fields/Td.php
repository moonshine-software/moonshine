<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
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
     * @param  ?Closure(mixed $data, $td self): self $fields
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
        return value($this->conditionalFields, $this->getData(), $this);
    }

    public function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): static {
        return $this
            ->setRawValue($raw)
            ->setData($casted ?? $raw)
            ->setRowIndex($index);
    }

    /**
     * @param  Closure(mixed $data, MoonShineComponentAttributeBag $attributes, $td self): MoonShineComponentAttributeBag  $attributes
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

    public function resolveTdAttributes(mixed $data, MoonShineComponentAttributeBag $attributes): MoonShineComponentAttributeBag
    {
        return $this->hasTdAttributes()
            ? value($this->tdAttributes, $data, $attributes, $this)
            : $attributes;
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
                ->resolveFill($this->toRawValue(), $this->getData())
                ->beforeRender(fn (): string => $this->hasLabels() ? '' : (string) LineBreak::make())
                ->withoutWrapper($this->hasLabels())
                ->forcePreview())
            ->render();
    }
}
