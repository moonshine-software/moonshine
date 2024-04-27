<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;

/** @method static static make(array $files = [], bool $download = true, ?Closure $names = null, ?Closure $itemAttributes = null) */
final class Files extends MoonShineComponent
{
    protected string $view = 'moonshine::components.files';

    public function __construct(
        public array $files = [],
        public bool $download = true,
        public ?Closure $names = null,
        public ?Closure $itemAttributes = null,
    ) {
        parent::__construct();
    }
}
