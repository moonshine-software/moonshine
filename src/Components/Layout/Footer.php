<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use Closure;
use Illuminate\Support\Collection;

/**
 * @method static static make(array $components = [])
 */
class Footer extends WithComponents
{
    protected $except = ['copyright'];

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

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'menu' => $this->getMenu(),
            'copyright' => $this->getCopyright(),
        ];
    }

    public function getMenu(): Collection
    {
        return collect($this->menu);
    }
}
