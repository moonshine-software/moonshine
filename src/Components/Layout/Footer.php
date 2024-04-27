<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Components\AbstractWithComponents;

class Footer extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.footer';

    protected string|Closure $copyright = '';

    public array $menu = [];

    public function __construct(
        iterable $components = [],
        // anonymous component variables
        array $menu = [],
        string $copyright = ''
    ) {
        $this->copyright = $copyright;
        $this->menu = $menu;

        parent::__construct($components);
    }

    public function copyright(string|Closure $text): self
    {
        $this->copyright = $text;

        return $this;
    }

    public function getCopyright(): string
    {
        return value($this->copyright);
    }

    /**
     * @param  array{string, string}  $data
     * @return $this
     */
    public function menu(array $data): self
    {
        $this->menu = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'menu' => $this->getMenu(),
            'copyright' => $this->getCopyright(),
        ];
    }

    public function getMenu(): Collection
    {
        return collect($this->menu);
    }
}
