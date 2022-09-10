<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use Leeto\MoonShine\Contracts\HasEndpoint;
use Leeto\MoonShine\Contracts\Resources\ResourceContract;
use Leeto\MoonShine\Contracts\ViewComponentContract;
use Leeto\MoonShine\Contracts\ViewContract;
use Leeto\MoonShine\Exceptions\ViewComponentException;
use Leeto\MoonShine\MoonShineRouter;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithComponent;
use Leeto\MoonShine\Traits\WithUriKey;
use Leeto\MoonShine\ViewComponents\ViewComponents;

class MoonShineView implements HasEndpoint, ViewContract
{
    use Makeable;
    use WithComponent;
    use WithUriKey;

    public function __construct(
        protected ResourceContract $resource
    ) {
    }

    public function resource(): ResourceContract
    {
        return $this->resource;
    }

    public static function resolveRoutes(): void
    {
        //
    }

    public function endpoint(): string
    {
        return MoonShineRouter::to(
            'view', [
                'resourceUri' => $this->resource()->uriKey(),
                'viewUri' => $this->uriKey(),
            ]
        );
    }

    public function components(): ViewComponents
    {
        return ViewComponents::make([]);
    }

    /**
     * @throws ViewComponentException
     */
    public function resolveComponent($componentClass): ViewComponentContract
    {
        $method = str($componentClass)
            ->classBasename()
            ->prepend('resolve')
            ->value();

        if (!method_exists($this, $method)) {
            throw ViewComponentException::notFoundInView(static::class);
        }

        return $this->{$method}();
    }

    public function jsonSerialize(): array
    {
        return [
            'endpoint' => $this->endpoint(),
            'component' => $this->getComponent(),
            'components' => $this->components(),
        ];
    }
}
