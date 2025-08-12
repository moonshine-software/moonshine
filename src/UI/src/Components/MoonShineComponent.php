<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Component;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Core\Traits\WithAssets;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithViewRenderer;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\UI\Traits\HasCanSee;

abstract class MoonShineComponent extends Component implements
    ComponentContract
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

        if (! $this->isConsoleMode()) {
            $this->resolveAssets();
        }

        $this->booted();
    }

    protected function booted(): void
    {
        //
    }

    public static function consoleMode(bool $enable = true): void
    {
        static::$consoleMode = $enable;
    }

    public function isConsoleMode(): bool
    {
        return static::$consoleMode;
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

    /**
     * @internal
     * Method is called after rendering
     *
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): static
    {
        /** @phpstan-ignore-next-line */
        $this->attributes = $this->attributes ?: $this->newAttributeBag();
        $this->attributes->setAttributes(
            array_merge($this->attributes->jsonSerialize(), $attributes),
        );

        return $this;
    }

    /**
     * @internal
     *
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return array_merge($this->extractPublicProperties(), [
            'attributes' => $this->getAttributes(),
            'name' => $this->getName(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return $this->data();
    }

    public function __clone(): void
    {
        $this->flushRenderCache();
        $this->onClone();
    }

    protected function onClone(): void
    {
        //
    }
}
