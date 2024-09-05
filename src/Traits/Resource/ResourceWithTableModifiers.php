<?php

declare(strict_types=1);

namespace MoonShine\Traits\Resource;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Components\TableBuilder;
use MoonShine\Fields\Fields;

trait ResourceWithTableModifiers
{
    public function trAttributes(): Closure
    {
        return fn (mixed $data, int $row, ComponentAttributeBag $attr): ComponentAttributeBag => $attr;
    }

    public function tdAttributes(): Closure
    {
        return fn (mixed $data, int $row, int $cell, ComponentAttributeBag $attr): ComponentAttributeBag => $attr;
    }

    /**
     * @return ?Closure(Fields $fields, TableBuilder $ctx): string
     */
    public function thead(): ?Closure
    {
        return null;
    }

    /**
     * @return  ?Closure(Collection $rows, TableBuilder $ctx): string
     */
    public function tbody(): ?Closure
    {
        return null;
    }

    /**
     * @return  ?Closure(ActionButtons $bulkButtons, TableBuilder $ctx): string
     */
    public function tfoot(): ?Closure
    {
        return null;
    }

    /**
     * @return  ?Closure(Collection $rows, TableBuilder $ctx): string
     */
    public function tbodyBefore(): ?Closure
    {
        return null;
    }

    /**
     * @return  ?Closure(Collection $rows, TableBuilder $ctx): string
     */
    public function tbodyAfter(): ?Closure
    {
        return null;
    }

}
