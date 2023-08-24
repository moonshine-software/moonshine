<?php

declare(strict_types=1);

namespace MoonShine\Resources;

class MoonShineProfileCustomPage extends CustomPage
{
    public string $alias = 'profile';

    public function __construct()
    {
        parent::__construct(
            $this->title(),
            $this->alias(),
            $this->view()
        );
    }

    public function view(): string
    {
        return 'moonshine::profile';
    }

    public function title(): string
    {
        return trans('moonshine::ui.profile');
    }

    public function datas(): array
    {
        return [];
    }
}
