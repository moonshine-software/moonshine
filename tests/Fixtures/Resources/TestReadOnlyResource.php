<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Resources;

use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

class TestReadOnlyResource extends TestResource
{
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->only(Action::VIEW);
    }
}
