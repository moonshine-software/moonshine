<?php

declare(strict_types=1);

namespace MoonShine\Components;

/** @method static static make(string $icon = 'heroicons.bell-alert', string $type = 'default', bool $removable = false) */
final class Alert extends MoonShineComponent
{
    protected string $view = 'moonshine::components.alert';

    public function __construct(
        public string $icon = 'heroicons.bell-alert',
        public string $type = 'default',
        public bool $removable = false,
    ) {
    }
}
