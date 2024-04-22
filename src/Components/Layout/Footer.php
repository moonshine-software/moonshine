<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Closure;
use Illuminate\Support\Collection;

class Footer extends WithComponents
{
    protected string $view = 'moonshine::components.layout.footer';

    protected string|Closure $copyright = '';

    protected array $menu = [];

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
            ...parent::viewData(),
            '_menu' => $this->getMenu(),
            '_copyright' => $this->getCopyright(),
        ];
    }

    public function getMenu(): Collection
    {
        return collect($this->menu);
    }
}
