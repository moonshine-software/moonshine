<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\UI\Components\AbstractWithComponents;

class Footer extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.footer';

    public function __construct(
        iterable $components = [],
        // anonymous component variables
        protected array $menu = [],
        protected string|Closure $copyright = ''
    ) {
        parent::__construct($components);
    }

    public function copyright(string|Closure $text): static
    {
        $this->copyright = $text;

        return $this;
    }

    public function getCopyright(): string
    {
        return value($this->copyright);
    }

    /**
     * @param  array<string, string>  $data
     * @return $this
     */
    public function menu(array $data): static
    {
        $this->menu = $data;

        return $this;
    }

    public function getMenu(): Collection
    {
        return collect($this->menu);
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
}
