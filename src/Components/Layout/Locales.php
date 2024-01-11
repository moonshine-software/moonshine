<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Illuminate\Support\Collection;
use MoonShine\Components\MoonShineComponent;

final class Locales extends MoonShineComponent
{
    protected string $view = 'moonshine::layouts.shared.locales';

    public string $current;

    public Collection $locales;

    public function __construct()
    {
        $this->current = app()->getLocale();
        $this->locales = collect(config('moonshine.locales', []))
            ->mapWithKeys(static fn ($locale): array => [
                request()->fullUrlWithQuery([
                    'change-moonshine-locale' => $locale,
                ]) => $locale,
            ]);
    }
}
