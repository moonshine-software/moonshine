<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Core\Traits\WithAssets;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithViewRenderer;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\UI\Traits\HasCanSee;

abstract class MoonShineComponent extends Component implements RenderableContract, HasCanSeeContract
{
    use Conditionable;
    use Macroable;
    use Makeable;
    use WithCore;
    use WithViewRenderer;
    use HasCanSee;
    use WithComponentAttributes;
    use WithAssets;

    protected static bool $consoleMode = false;

    public function __construct(
        protected string $name = 'default',
    ) {
        $this->attributes = new MoonShineComponentAttributeBag();

        if($this instanceof HasAssetsContract && ! $this->isConsoleMode()) {
            $this->resolveAssets();
        }
    }

    public static function consoleMode(bool $enable = true): void
    {
        static::$consoleMode = $enable;
    }

    public function isConsoleMode(): bool
    {
        return static::$consoleMode;
    }

    public function getAssetManager(): AssetManagerContract
    {
        return $this->getCore()->getContainer(AssetManagerContract::class);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @internal  */
    public function data(): array
    {
        return array_merge($this->extractPublicProperties(), [
            'attributes' => $this->getAttributes(),
            'name' => $this->getName(),
        ]);
    }

    protected function systemViewData(): array
    {
        return $this->data();
    }
}
