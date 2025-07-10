<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

interface NowOnContract
{
    /**
     * @param  array<string, mixed>  $params
     */
    public function nowOn(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $params = []
    ): static;

    public function nowOnPage(PageContract $page): static;

    public function nowOnResource(ResourceContract $resource): static;

    /**
     * @param  array<string, mixed>  $params
     */
    public function nowOnParams(array $params): static;

    public function getNowOnResource(): ?ResourceContract;

    public function getNowOnPage(): ?PageContract;

    /**
     * @return   array<string, mixed>
     */
    public function getNowOnQueryParams(): array;
}
