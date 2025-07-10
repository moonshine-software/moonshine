<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\AbstractWithComponents;
use Throwable;

class Footer extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.footer';

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     * @param  array<string, string>  $menu
     *
     * @throws Throwable
     */
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
     */
    public function menu(array $data): static
    {
        $this->menu = $data;

        return $this;
    }

    /**
     * @return Collection<string, string>
     */
    public function getMenu(): Collection
    {
        return new Collection($this->menu);
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
