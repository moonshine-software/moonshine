<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\UI\Collections\ComponentsCollection;

interface PageView
{
    public function components(PageContract $page): ComponentsCollection;
}
