<?php

namespace Leeto\MoonShine\Contracts\Components;

interface ViewComponentContract
{
    public static function make(...$arguments): static;

    public function id($index = null): string;

    public function name($index = null): string;

    public function label(): string;

    public function setLabel(string $label): static;

    public function getView(): string;
}