<?php

declare(strict_types=1);

namespace MoonShine\Components\Layout;

use MoonShine\Components\MoonShineComponent;
use MoonShine\Support\MoonShineComponentAttributeBag;

/**
 * @method static static make(string $href,string $logo,?string $logoSmall = null,?string $title = null)
 */
final class Logo extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.logo';

    public MoonShineComponentAttributeBag $logoAttributes;

    public MoonShineComponentAttributeBag $logoSmallAttributes;

    public function __construct(
        public string $href,
        public string $logo,
        public ?string $logoSmall = null,
        public ?string $title = null,
    ) {
        parent::__construct();

        $this->title ??= config('moonshine.title');
        $this->logoAttributes = new MoonShineComponentAttributeBag();
        $this->logoSmallAttributes = new MoonShineComponentAttributeBag();
    }

    public function logoAttributes(array $attributes): self
    {
        $this->logoAttributes = $this->logoAttributes->merge($attributes);

        return $this;
    }

    public function logoSmallAttributes(array $attributes): self
    {
        $this->logoSmallAttributes = $this->logoSmallAttributes->merge($attributes);

        return $this;
    }

    public function minimized(): self
    {
        return $this->logoAttributes([
            ':class' => "minimizedMenu && '!hidden'",
        ])->logoSmallAttributes([
            ':class' => "minimizedMenu && '!block'",
        ]);
    }
}
