<?php

declare(strict_types=1);

namespace MoonShine\Core\Traits;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

trait WithViewRenderer
{
    protected string $view = '';

    protected ?string $customView = null;

    /**
     * @var array<string, mixed>|Closure
     */
    protected array|Closure $customViewData = [];

    protected ?Closure $onBeforeRenderCallback = null;

    private Renderable|Closure|string|null $cachedRender = null;

    protected array $translates = [];

    public function getTranslates(): array
    {
        return collect($this->translates)
            ->mapWithKeys(fn (string $key, string $name) => [$name => $this->getCore()->getTranslator()->get($key)])
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

    protected function resolveAssets(): void
    {
        $this->getAssetManager()->add($this->getAssets());
    }

    public function shouldRender(): bool
    {
        return $this instanceof HasCanSeeContract
            ? $this->isSee(...$this->isSeeParams())
            : true;
    }

    public function onBeforeRender(Closure $onBeforeRender): static
    {
        $this->onBeforeRenderCallback = $onBeforeRender;

        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function render(): Renderable|Closure|string
    {
        if(! $this->shouldRender()) {
            return '';
        }

        if (! is_null($this->cachedRender)) {
            return $this->cachedRender;
        }

        $this->prepareBeforeRender();

        if(! is_null($this->onBeforeRenderCallback)) {
            value($this->onBeforeRenderCallback, $this);
        }

        $view = $this->resolveRender();

        return $this->cachedRender = $this->prepareRender($view);
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

    public function toStructure(bool $withStates = true): array
    {
        $components = [];
        $states = $withStates ? $this->toArray() : [];

        $states = data_forget($states, 'componentName');
        $states = data_forget($states, 'components');
        $states = data_forget($states, 'fields');

        if($this instanceof HasComponentsContract) {
            $components = $this->getComponents()
                ->map(static fn (RenderableContract $component): array => $component->toStructure($withStates));
        }

        if($this instanceof HasFieldsContract) {
            $components = $this->getFields()
                ->map(static fn (RenderableContract $component): array => $component->toStructure($withStates));

            $states['fields'] = $components;
        }

        return array_filter([
            'type' => class_basename($this),
            'components' => $components,
            'states' => $states,
        ]);
    }

    public function toArray(): array
    {
        return [
            ...$this->viewData(),
            ...$this->getCustomViewData(),
            ...$this->systemViewData(),
            'translates' => $this->getTranslates(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return (string) value($this->render(), $this);
    }

    public function escapeWhenCastingToString($escape = true): static
    {
        return $this;
    }
}
