<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\Pages\Page;
use MoonShine\UI\Collections\ComponentsCollection;

interface PageView
{
    public function components(Page $page): ComponentsCollection;
}
