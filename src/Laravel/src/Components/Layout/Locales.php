<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components\Layout;

use Illuminate\Support\Collection;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\Http\Middleware\ChangeLocale;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @use WithCore<MoonShine>
 */
final class Locales extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.locales';

    public string $current;

    public Collection $locales;

    public function __construct()
    {
        parent::__construct();

        $this->current = $this->getCore()->getConfig()->getLocale();
        $this->locales = collect($this->getCore()->getConfig()->getLocales())
            ->mapWithKeys(fn ($locale): array => [
                $this->getCore()->getRequest()->getUrlWithQuery([
                    ChangeLocale::KEY => $locale,
                ]) => $locale,
            ]);
    }
}
