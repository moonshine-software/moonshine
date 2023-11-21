<?php

declare(strict_types=1);

namespace MoonShine\Table;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
use MoonShine\Traits\Makeable;
use Throwable;

final class TableRow
{
    use Makeable;

    public function __construct(
        protected mixed $data,
        protected Fields $fields,
        protected ActionButtons $actions,
        protected ?Closure $trAttributes = null,
        protected ?Closure $tdAttributes = null,
        protected ?Closure $systemTrAttributes = null,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function getKey(): int|string
    {
        return $this->getFields()
            ->findByClass(ID::class)
            ?->value() ?? '';
    }

    /**
     * @throws Throwable
     */
    public function hasKey(): bool
    {
        return $this->getKey() !== 0 && $this->getKey() !== '';
    }

    public function getFields(): Fields
    {
        return $this->fields;
    }

    public function getActions(): ActionButtons
    {
        return $this->actions;
    }

    public function trAttributes(int $row): ComponentAttributeBag
    {
        $attributes = new ComponentAttributeBag();

        if (is_null($this->trAttributes)) {
            return $attributes;
        }

        $attributes = value($this->trAttributes, $this->data, $row, $attributes);

        if (!is_null($this->systemTrAttributes)) {
            return value($this->systemTrAttributes, $this->data, $row, $attributes);
        }

        return $attributes;
    }

    public function tdAttributes(int $row, int $cell): ComponentAttributeBag
    {
        $attributes = new ComponentAttributeBag();

        if (is_null($this->tdAttributes)) {
            return $attributes;
        }

        return value($this->tdAttributes, $this->data, $row, $cell, $attributes);
    }
}
