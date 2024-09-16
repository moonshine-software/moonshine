<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;

interface NowOnContract
{
    public function nowOn(
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
        array $params = []
    ): static;

    public function nowOnPage(PageContract $page): static;

    public function nowOnResource(ResourceContract $resource): static;

    public function nowOnParams(array $params): static;

    public function getNowOnResource(): ?ResourceContract;

    public function getNowOnPage(): ?PageContract;

    public function getNowOnQueryParams(): array;
}
