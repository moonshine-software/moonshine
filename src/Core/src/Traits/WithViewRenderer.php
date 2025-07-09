<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasFieldsContract;

/**
 * @mixin WithAssets
 */
trait WithViewRenderer
{
    protected string $view = '';

    protected ?string $customView = null;

    /**
     * @var array<string, mixed>|(Closure(static): array<string, mixed>)
     */
    protected array|Closure $customViewData = [];

    protected ?Closure $onBeforeRenderCallback = null;

    private Renderable|Closure|string|null $cachedRender = null;

    /**
     * @var array<string, string>
     */
    protected array $translates = [];

    /**
     * @return array<string, string>
     */
    public function getTranslates(): array
    {
        /**
         * @var Collection<string, string> $collection
         */
        $collection = new Collection($this->translates);

        /**
         * @var array<string, string>
         */
        return $collection
            ->mapWithKeys(fn (string $key, string $name): array => [$name => $this->getCore()->getTranslator()->get($key)])
            ->toArray();
    }

    public function getView(): string
    {
        return $this->customView ?? $this->view;
    }

    /**
     * @return array<string, mixed>
     */
    public function getCustomViewData(): array
    {
        return value($this->customViewData, $this);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function customView(string $view, array $data = []): static
    {
        $this->customView = $view;
        $this->customViewData = $data;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [];
    }

    protected function prepareBeforeRender(): void
    {
        //
    }

    protected function prepareBeforeSerialize(): void
    {
        //
    }

    protected function resolveAssets(): void
    {
        $this->getAssetManager()->add($this->getAssets());
    }

    public function shouldRender(): bool
    {
        return ! $this instanceof HasCanSeeContract || $this->isSee();
    }

    public function onBeforeRender(Closure $onBeforeRender): static
    {
        $this->onBeforeRenderCallback = $onBeforeRender;

        return $this;
    }

    public function render(): Renderable|Closure|string
    {
        if (! $this->shouldRender()) {
            return '';
        }

        if (! \is_null($this->cachedRender)) {
            return $this->cachedRender;
        }

        $this->prepareBeforeRender();

        if (! \is_null($this->onBeforeRenderCallback)) {
            \call_user_func($this->onBeforeRenderCallback, $this);
        }

        $view = $this->resolveRender();

        return $this->cachedRender = $this->prepareRender($view);
    }

    public function flushRenderCache(): static
    {
        $this->cachedRender = null;

        return $this;
    }

    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        return $view;
    }

    protected function resolveRender(): Renderable|Closure|string
    {
        return $this->renderView();
    }

    protected function renderView(): Renderable|Closure|string
    {
        return $this->getCore()->getRenderer()->render(
            $this->getView(),
            $this->toArray(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toStructure(bool $withStates = true): array
    {
        $components = [];
        $states = $withStates ? $this->toArray() : [];

        Arr::forget($states, ['componentName', 'components', 'fields']);

        if ($this instanceof HasComponentsContract) {
            /** @var Collection<array-key, ComponentContract> $componentsCollection */
            $componentsCollection = $this->getComponents();

            $components = $componentsCollection
                ->map(static fn (ComponentContract $component): array => $component->toStructure($withStates));
        }

        if ($this instanceof HasFieldsContract) {
            /** @var Collection<array-key, ComponentContract> $fieldsCollection */
            $fieldsCollection = $this->getFields();

            $components = $fieldsCollection
                ->map(static fn (ComponentContract $component): array => $component->toStructure($withStates));

            $states['fields'] = $components;
        }

        return array_filter([
            'type' => class_basename($this),
            'components' => $components,
            'states' => $states,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $this->prepareBeforeSerialize();

        return [
            ...$this->viewData(),
            ...$this->getCustomViewData(),
            ...$this->systemViewData(),
            'translates' => $this->getTranslates(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        /** @phpstan-ignore cast.string */
        return (string) value($this->render(), $this);
    }

    /**
     * @param bool $escape
     */
    public function escapeWhenCastingToString($escape = true): static
    {
        return $this;
    }
}
