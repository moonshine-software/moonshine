<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

final class Loader extends MoonShineComponent
{
    protected string $view = 'moonshine::components.loader';

    protected static ?string $changedView = null;

    public static function changeView(string $view): void
    {
        self::$changedView = $view;
    }

    public function getView(): string
    {
        if (! \is_null(self::$changedView)) {
            return self::$changedView;
        }

        return parent::getView();
    }
}
