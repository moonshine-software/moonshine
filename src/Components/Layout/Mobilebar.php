<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

class Mobilebar extends WithComponents
{
    protected string $view = 'moonshine::components.layout.mobilebar';

    protected bool $hideLogo = false;

    protected bool $hideSwitcher = false;

    public function hideLogo(): self
    {
        $this->hideLogo = true;

        return $this;
    }

    public function hideSwitcher(): self
    {
        $this->hideSwitcher = true;

        return $this;
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'hideLogo' => $this->hideLogo,
            'hideSwitcher' => $this->hideSwitcher,
        ];
    }
}
