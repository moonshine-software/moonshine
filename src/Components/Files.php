<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(array $files = [], bool $download = true) */
final class Files extends MoonShineComponent
{
    protected string $view = 'moonshine::components.files';

    public function __construct(
        public array $files = [],
        public bool $download = true,
    ) {
    }
}
