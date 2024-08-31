<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Resource;

use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\TableRowsContract;

trait ResourceWithTableModifiers
{
    protected ?TableRowsContract $thead = null;

    protected ?TableRowsContract $tbody = null;

    protected ?TableRowsContract $tfoot = null;

    protected function thead(): ?TableRowsContract
    {
        return null;
    }

    public function getHeadRows(): ?TableRowsContract
    {
        if (! is_null($this->thead)) {
            return $this->thead;
        }

        return $this->thead = $this->thead();
    }

    protected function tbody(): ?TableRowsContract
    {
        return null;
    }

    public function getRows(): ?TableRowsContract
    {
        if (! is_null($this->tbody)) {
            return $this->tbody;
        }

        return $this->tbody = $this->tbody();
    }

    protected function tfoot(): ?TableRowsContract
    {
        return null;
    }

    public function getFootRows(): ?TableRowsContract
    {
        if (! is_null($this->tfoot)) {
            return $this->tfoot;
        }

        return $this->tfoot = $this->tfoot();
    }

    protected function trAttributes(): Closure
    {
        return static fn (?DataWrapperContract $data, int $row): array => [];
    }

    public function getTrAttributes(): Closure
    {
        return $this->trAttributes();
    }

    protected function tdAttributes(): Closure
    {
        return static fn (?DataWrapperContract $data, int $row, int $cell): array => [];
    }

    public function getTdAttributes(): Closure
    {
        return $this->tdAttributes();
    }
}
