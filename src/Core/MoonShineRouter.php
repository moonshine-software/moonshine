<?php

declare(strict_types=1);

namespace MoonShine\Core;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Core\Contracts\MoonShineEndpoints;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\ResourceContract;
use Stringable;

abstract class MoonShineRouter implements Stringable
{
    use Conditionable;

    public const ROUTE_PREFIX = 'moonshine';

    public function __construct(
        private string $name = '',
        private array $params = [],
    ) {
    }

    abstract public function to(string $name = '', array $params = []): string;

    public function getEndpoints(): MoonShineEndpoints
    {
        return moonshine()->getContainer(MoonShineEndpoints::class, null, $this);
    }

    public function withName(string $name): self
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
        return str($this->getRawName())
            ->prepend(self::ROUTE_PREFIX . '.')
            ->when(
                $name,
                fn (Stringable $str) => $str
                    ->trim('.')
                    ->append(".$name")
            )
            ->value();
    }

    public function withParams(array $params): self
    {
        $this->params = array_merge(
            $params,
            $this->params,
        );

        return $this;
    }

    public function withPage(?PageContract $page = null): self
    {
        if (! is_null($pageUri = $this->extractPageUri($page))) {
            return $this->withParams([
                'pageUri' => $pageUri,
            ]);
        }

        return $this;
    }

    public function withResource(?ResourceContract $resource = null): self
    {
        if (! is_null($resourceUri = $this->extractResourceUri($resource))) {
            return $this->withParams([
                'resourceUri' => $resourceUri,
            ]);
        }

        return $this;
    }

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

    public function forgetParam(string $key): self
    {
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

    /**
     * @param  class-string  $class
     */
    public static function uriKey(string $class): string
    {
        return str($class)
            ->classBasename()
            ->kebab()
            ->value();
    }

    public function __toString(): string
    {
        return $this->to();
    }
}
