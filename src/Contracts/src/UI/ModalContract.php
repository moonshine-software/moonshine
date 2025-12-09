<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface ModalContract
{
    public function open(Closure|bool|null $condition = null): self;

    public function closeOutside(Closure|bool|null $condition = null): self;

    public function wide(Closure|bool|null $condition = null): self;

    public function full(Closure|bool|null $condition = null): self;

    public function auto(Closure|bool|null $condition = null): self;

    public function autoClose(Closure|bool|null $condition = null): self;

    /**
     * @param  array<string, mixed>  $attributes
     *
     */
    public function outerAttributes(array $attributes): self;

    /**
     * @param string[] $events
     */
    public function toggleEvents(array $events, bool $onlyOpening = false, bool $onlyClosing = false): self;

    public function alwaysLoad(): self;
}
