<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(bool $value) */
final class Boolean extends MoonShineComponent
{
    protected string $view = 'moonshine::components.boolean';

    public function __construct(public bool $value)
    {
        parent::__construct();
    }
}
