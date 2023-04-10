<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use MoonShine\Contracts\ResourceRenderable;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithColumnSpan;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

abstract class DashboardItem implements ResourceRenderable
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithColumnSpan;
}
