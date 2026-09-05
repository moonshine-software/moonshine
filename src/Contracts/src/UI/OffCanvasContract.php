<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface OffCanvasContract extends ComponentContract
{
    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function open(Closure|bool|null $condition = null): self;

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function left(Closure|bool|null $condition = null): self;

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function wide(Closure|bool|null $condition = null): self;

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function full(Closure|bool|null $condition = null): self;

    /**
     * @param  array<string, mixed>  $attributes
     *
     */
    public function togglerAttributes(array $attributes): self;

    /**
     * @param string[] $events
     */
    public function toggleEvents(array $events, bool $onlyOpening = false, bool $onlyClosing = false): self;

    public function alwaysLoad(): self;
}
