<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Extensions;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class Extension
{
    public array $tabs = [];

    public function fields(): array
    {
        return [];
    }

    // todo: check param
    public function tabs(Model $item = null): Factory|View|Application
    {
        return view('moonshine::shared.tabs', ['tabs' => $this->tabs]);
    }
}
