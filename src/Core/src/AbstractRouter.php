<?php

declare(strict_types=1);

namespace MoonShine\Core;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\EndpointsContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use Stringable;

abstract class AbstractRouter implements RouterContract, Stringable
{
    use Conditionable;

    public const ROUTE_PREFIX = 'moonshine';

    private string $name = '';

    /**
     * @var array<string, mixed>
     */
    private array $params = [];

    /**
     * @param  array<string, mixed>  $params
     */
    abstract public function to(string $name = '', array $params = []): string;

    abstract public function getEndpoints(): EndpointsContract;

    public function withName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRawName(): string
    {
        return $this->name;
    }

    public function getName(string $name = ''): string
    {
        return Str::of($this->getRawName())
            ->prepend(self::ROUTE_PREFIX . '.')
            ->when(
                $name,
                static fn (Stringable $str) => $str
                    ->trim('.')
                    ->append(".$name")
            )
            ->value();
    }

    /**
     * @param  array<string, mixed>  $params
     */
    public function withParams(array $params): static
    {
        $this->params = array_merge(
            $params,
            $this->params,
        );

        return $this;
    }

    public function withPage(?PageContract $page = null): static
    {
        if (! \is_null($pageUri = $this->extractPageUri($page))) {
            return $this->withParams([
                'pageUri' => $pageUri,
            ]);
        }

        return $this;
    }

    public function withResource(?ResourceContract $resource = null): static
    {
        if (! \is_null($resourceUri = $this->extractResourceUri($resource))) {
            return $this->withParams([
                'resourceUri' => $resourceUri,
            ]);
        }

        return $this;
    }

    public function withResourceItem(int|string|null $key = null, ?CrudResourceContract $resource = null): static
    {
        if (! \is_null($key = $this->extractResourceItem($key, $resource))) {
            return $this->withParams([
                'resourceItem' => $key,
            ]);
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function getParams(array $params = []): array
    {
        return array_filter(
            array_merge(
                $this->params,
                $params
            ),
            static fn ($value) => filled($value)
        );
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        return data_get($this->getParams(), $key, $default);
    }

    public function forgetParam(string $key): static
    {
        /** @phpstan-ignore-next-line  */
        data_forget($this->params, $key);

        return $this;
    }

    public function flushState(): void
    {
        $this->params = [];
        $this->name = '';
    }

    public function extractPageUri(?PageContract $page = null): ?string
    {
        return null;
    }

    public function extractResourceUri(?ResourceContract $resource = null): ?string
    {
        return null;
    }

    public function extractResourceItem(
        int|string|null $key = null,
        ?CrudResourceContract $resource = null
    ): string|int|null {
        return null;
    }

    public function __toString(): string
    {
        return $this->to();
    }
}
