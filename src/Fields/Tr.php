<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Components\FieldsGroup;
use MoonShine\Decorations\LineBreak;
use Throwable;

/**
 * @method static static make(array $cells)
 */
class Tr extends Template
{
    private array $cells = [];

    private ?Closure $trAttributes = null;

    public function __construct(array $cells = null)
    {
        parent::__construct();

        $this->cells($cells);
    }


    public function cells(array $cells): self
    {
        $this->cells = $cells;

        return $this;
    }

    public function hasCells(): bool
    {
        return $this->cells !== [];
    }

    public function getCells(): Fields
    {
        return $this->hasCells() ? Fields::make($this->cells) : $this->getFields();
    }

    public function resolveFill(
        array $raw = [],
        mixed $casted = null,
        int $index = 0
    ): static {
        $this->getCells()
            ->onlyFields()
            ->each(fn (Field $field): Field => $field->resolveFill($raw, $casted, $index));

        return $this
            ->setRawValue($raw)
            ->setData($casted ?? $raw)
            ->setRowIndex($index);
    }

    /**
     * @param  Closure(mixed $data, int $row, int $cell, ComponentAttributeBag $attributes, $tr self): ComponentAttributeBag  $attributes
     * @return self
     */
    public function trAttributes(Closure $attributes): self
    {
        $this->trAttributes = $attributes;

        return $this;
    }

    public function hasTrAttributes(): bool
    {
        return ! is_null($this->trAttributes);
    }

    public function resolveTrAttributes(mixed $data, int $row, ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        return $this->hasTrAttributes()
            ? value($this->trAttributes, $data, $row, $attributes, $this)
            : $attributes;
    }

    /**
     * @throws Throwable
     */
    public function __clone()
    {
        $fields = [];

        foreach ($this->getCells() as $index => $field) {
            $fields[$index] = clone $field;
        }

        $this->cells($fields);
    }
}
