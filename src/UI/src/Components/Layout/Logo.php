<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\MoonShineComponent;

/**
 * @method static static make(string $href,string $logo,?string $logoSmall = null,?string $title = null)
 */
final class Logo extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.logo';

    public ComponentAttributesBagContract $logoAttributes;

    public ComponentAttributesBagContract $logoSmallAttributes;

    public function __construct(
        public string $href,
        public string $logo,
        public ?string $logoSmall = null,
        public ?string $title = null,
        // anonymous component variables
        public ?string $darkLogo = null,
        public ?string $darkLogoSmall = null,
        public bool $minimized = false,
    ) {
        parent::__construct();

        $this->title ??= $this->getCore()->getConfig()->getTitle();
        $this->logoAttributes = new MoonShineComponentAttributeBag();
        $this->logoSmallAttributes = new MoonShineComponentAttributeBag();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function logoAttributes(array $attributes): self
    {
        $this->logoAttributes = $this->logoAttributes->merge($attributes);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function logoSmallAttributes(array $attributes): self
    {
        $this->logoSmallAttributes = $this->logoSmallAttributes->merge($attributes);

        return $this;
    }

    public function minimized(): self
    {
        $this->minimized = true;

        return $this;
    }

    public function darkMode(string $logo, ?string $small = null): self
    {
        $this->darkLogo = $logo;
        $this->darkLogoSmall = $small;

        return $this;
    }

    protected function prepareBeforeRender(): void
    {
        if ($this->minimized) {
            $this->logoAttributes([
                ':class' => "minimizedMenu && '!hidden'",
            ])->logoSmallAttributes([
                ':class' => "minimizedMenu && '!block'",
            ]);
        }
    }
}
