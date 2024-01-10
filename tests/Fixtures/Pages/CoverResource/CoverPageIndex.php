<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Pages\CoverResource;

use MoonShine\Pages\Crud\IndexPage;

class CoverPageIndex extends IndexPage
{
    public function fields(): array
    {
        return [];
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer(),
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer(),
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer(),
        ];
    }
}
