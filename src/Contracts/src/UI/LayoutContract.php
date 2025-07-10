<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\PageContract;

interface LayoutContract
{
    public function setPage(PageContract $page): static;

    public function build(): ComponentContract;
}
