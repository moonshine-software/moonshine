<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithColumnSpan;
use Leeto\MoonShine\Traits\WithLabel;
use Leeto\MoonShine\Traits\WithView;

abstract class DashboardItem implements HtmlViewable
{
    use Makeable;
    use WithView;
    use WithLabel;
    use WithColumnSpan;
}
