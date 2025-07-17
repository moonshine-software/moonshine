<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

/**
 * @template TResource of ResourceContract = ResourceContract
 */
interface EndpointsContract
{
    /**
     * @param class-string<PageContract>|PageContract|null $page
     * @param class-string<TResource>|TResource|null  $resource
     * @param array<string, mixed> $params
     * @param array<string, mixed> $extra
     *
     */
    public function toPage(
        string|PageContract|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): mixed;

    /**
     * @param  ?TResource  $resource
     * @param array<string, mixed> $extra
     *
     */
    public function updateField(
        ?ResourceContract $resource = null,
        ?PageContract $page = null,
        array $extra = []
    ): string;

    /**
     * @param  ?TResource  $resource
     * @param  array<string, mixed>  $params
     */
    public function method(
        string $method,
        ?string $message = null,
        array $params = [],
        ?PageContract $page = null,
        ?ResourceContract $resource = null
    ): string;


    /**
     * @param  array<string, mixed>  $additionally
     */
    public function component(
        string $name,
        array $additionally = []
    ): string;

    /**
     * @param ?TResource  $resource
     * @param array<string, mixed> $extra
     */
    public function reactive(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $extra = []
    ): string;

    public function withRelation(
        string $action,
        int|string|null $resourceItem = null,
        ?string $relation = null,
        ?string $resourceUri = null,
        ?string $pageUri = null,
        ?string $parentField = null,
    ): string;

    public function home(): string;
}
