<?php

declare(strict_types=1);

namespace MoonShine\Contracts;

use MoonShine\Pages\Page;
use MoonShine\Pages\PageComponents;

interface PageView
{
    public function components(Page $page): PageComponents;
}
