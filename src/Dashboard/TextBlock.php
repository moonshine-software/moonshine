<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithLabel;
use Leeto\MoonShine\Traits\WithView;

final class TextBlock extends DashboardItem
{
    use Makeable;

    public function __construct(
        string $label,
        protected string $text,
    ) {
        $this->setLabel($label);
    }

    public function text(): string
    {
        return $this->text;
    }

    public function id(string $index = null): string
    {
        return str(uniqid('', true))->slug('_')->value();
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    public function getView(): string
    {
        return 'moonshine::blocks.text';
    }
}
