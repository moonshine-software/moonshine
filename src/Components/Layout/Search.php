<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;

/**
 * @method static static make(string $key = 'search')
 */
final class Search extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.search';

    public function __construct(
        public string $key = 'search'
    ) {
    }

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

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $action = moonshineRouter()->to('global-search');

        if (! $this->globalSearchEnabled() && $this->resourceSearchEnabled()) {
            $action = moonshineRequest()->getResource()?->url();
        }

        return [
            'isEnabled' => $this->globalSearchEnabled() || $this->resourceSearchEnabled(),
            '_action' => $action,
            'isGlobal' => $this->globalSearchEnabled(),
            'value' => request($this->key, ''),
        ];
    }
}
