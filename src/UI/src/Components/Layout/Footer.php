<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\DisplayHtml;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Enums\DisplayTextType;
use MoonShine\UI\Traits\WithLabelEscaping;
use Throwable;

class Footer extends AbstractWithComponents
{
    use WithLabelEscaping;

    protected string $view = 'moonshine::components.layout.footer';

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     * @param  array<string, string>  $menu
     *
     * @throws Throwable
     * @param (Closure(): string)|string $copyright
     */
    public function __construct(
        iterable $components = [],
        // anonymous component variables
        protected array $menu = [],
        protected string|Closure $copyright = '',
        ?bool $escapeLabel = null,
    ) {
        $this->setDisplayEscaping(DisplayTextType::LABEL, $escapeLabel);

        parent::__construct($components);
    }

    /**
     * @param (Closure(): string)|string $text
     */
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
     * @param  array<string, mixed>  $customViewData
     * @return array<string, mixed>
     */
    protected function resolveCustomViewData(array $customViewData): array
    {
        $menu = $customViewData['menu'] ?? null;

        if (is_iterable($menu) && ! \array_key_exists('menuLabelsHtml', $customViewData)) {
            $labels = iterator_to_array($menu);
            $customViewData['menuLabelsHtml'] = new Collection($labels)->map(
                fn (mixed $label): DisplayHtml => DisplayHtml::make($label, $this->isEscapeLabel())
            );

            if (! \is_array($menu) && ! $menu instanceof Collection) {
                $customViewData['menu'] = $labels;
            }
        }

        return parent::resolveCustomViewData($customViewData);
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        $menu = $this->getMenu();

        return [
            'menu' => $menu,
            'menuLabelsHtml' => $menu->map(
                fn (string $label): DisplayHtml => DisplayHtml::make($label, $this->isEscapeLabel())
            ),
            'copyright' => $this->getCopyright(),
        ];
    }
}
