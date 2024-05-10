<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Illuminate\Support\Collection;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Http\Middleware\ChangeLocale;

final class Locales extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.locales';

    public string $current;

    public Collection $locales;

    public function __construct()
    {
        parent::__construct();

        $this->current = app()->getLocale();
        $this->locales = collect(moonshineConfig()->getLocales())
            ->mapWithKeys(static fn ($locale): array => [
                request()->fullUrlWithQuery([
                    ChangeLocale::KEY => $locale,
                ]) => $locale,
            ]);
    }
}
