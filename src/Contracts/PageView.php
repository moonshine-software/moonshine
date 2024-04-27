<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use MoonShine\Collections\ComponentsCollection;
use MoonShine\Pages\Page;

interface PageView
{
    public function components(Page $page): ComponentsCollection;
}
