<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

final class Burger extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.burger';

    public string $toggleMethod = 'toggleSidebar';

    public string $toggleState = 'isSidebarOpen';

    public function __construct(
        public bool $sidebar = false,
        public bool $topbar = false,
        public bool $mobileBar = false,
    ) {
        parent::__construct();

        if ($this->sidebar) {
            $this->sidebar();
        } elseif ($this->topbar) {
            $this->topbar();
        } elseif ($this->mobileBar) {
            $this->mobileBar();
        }
    }

    public function toggleMethod(string $method, string $state): static
    {
        $this->toggleMethod = $method;
        $this->toggleState = $state;

        return $this;
    }

    public function sidebar(): static
    {
        $this->toggleMethod = 'toggleSidebar';
        $this->toggleState = 'isSidebarOpen';

        return $this;
    }

    public function topbar(): static
    {
        $this->toggleMethod = 'toggleTopbar';
        $this->toggleState = 'isTopbarOpen';

        return $this;
    }

    public function mobileBar(): static
    {
        $this->toggleMethod = 'toggleMobileBar';
        $this->toggleState = 'isMobileBarOpen';

        return $this;
    }
}
