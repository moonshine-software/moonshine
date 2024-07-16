<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Core\Core;
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
    use WithViewRenderer;
    use HasCanSee;
    use WithComponentAttributes;

    // todo DI
    protected CoreContract $core;
    protected AssetManagerContract $assetManager;
    protected ColorManagerContract $colorManager;

    public function __construct(
        protected string $name = 'default',

    ) {
        $this->attributes = new MoonShineComponentAttributeBag();

        // todo DI
        $this->core = Core::getInstance();
        $this->assetManager = $this->core->getContainer(AssetManagerContract::class);
        $this->colorManager = $this->core->getContainer(ColorManagerContract::class);

        if($this instanceof HasAssetsContract) {
            $this->resolveAssets();
        }

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
