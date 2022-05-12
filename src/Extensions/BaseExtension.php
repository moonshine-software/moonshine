<?php

namespace Leeto\MoonShine\Extensions;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class BaseExtension
{
    public array $tabs = [];

    public function fields(): array
    {
        return [];
    }

    public function tabs(Model $item): Factory|View|Application
    {
        return view('moonshine::shared.tabs', ['tabs' => $this->tabs]);
    }
}