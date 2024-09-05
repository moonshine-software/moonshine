<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(string $key = 'search', string $action = '', string $placeholder = '')
 */
final class Search extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.search';

    public function __construct(
        private readonly string $key = 'search',
        private string $action = '',
        private string $placeholder = '',
    ) {
        parent::__construct();

        if ($this->placeholder === '') {
            $this->placeholder = __('moonshine::ui.search') . ' (Ctrl+K)';
        }
    }

    protected function isSearchEnabled(): bool
    {
        $resource = moonshineRequest()->getResource();

        return ! is_null($resource) && $resource->hasSearch();
    }

    protected function prepareBeforeRender(): void
    {
        if ($this->isSearchEnabled()) {
            $this->action = moonshineRequest()->getResource()?->getUrl();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'action' => $this->action,
            'value' => moonshine()->getRequest()->get($this->key, ''),
            'placeholder' => $this->placeholder,
            'isEnabled' => $this->isSearchEnabled(),
        ];
    }
}
