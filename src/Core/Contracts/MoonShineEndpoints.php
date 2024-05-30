<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\Pages\Page;

interface MoonShineEndpoints
{
    public function toPage(
        string|Page|null $page = null,
        string|ResourceContract|null $resource = null,
        array $params = [],
        array $extra = [],
    ): mixed;

    public function updateColumn(
        ?ResourceContract $resource = null,
        ?Page $page = null,
        array $extra = []
    ): string;

    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $params = [],
        ?Page $page = null,
        ?ResourceContract $resource = null
    ): string;

    public function asyncComponent(
        string $name,
        array $additionally = []
    ): string;

    public function reactive(
        ?Page $page = null,
        ?ResourceContract $resource = null,
        array $extra = []
    ): string;

    public function home(): string;
}
