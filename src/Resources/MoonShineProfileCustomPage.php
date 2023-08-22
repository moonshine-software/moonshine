<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Resources\CustomPage;

class MoonShineProfileCustomPage extends CustomPage
{

    public static string $alias = 'profile';

    public static string $view = 'moonshine::profile';

    public function title(): string
    {
        return trans('moonshine::ui.profile');
    }

    public function datas(): array
    {
        return [];
    }
}
