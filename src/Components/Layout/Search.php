<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make()
 */
final class Search extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.search';

    protected function globalSearchEnabled(): bool
    {
        return filled(
            config('moonshine.global_search', [])
        );
    }

    protected function resourceSearchEnabled(): bool
    {
        $resource = moonshineRequest()->getResource();

        return ! is_null($resource) && method_exists($resource, 'search') && $resource->search();
    }

    protected function viewData(): array
    {
        $action = moonshineRouter()->to('global-search');

        if (! $this->globalSearchEnabled() && $this->resourceSearchEnabled()) {
            $action = to_page(resource: moonshineRequest()->getResource());
        }

        return [
            'isEnabled' => $this->globalSearchEnabled() || $this->resourceSearchEnabled(),
            '_action' => $action,
            'isGlobal' => $this->globalSearchEnabled(),
        ];
    }
}
