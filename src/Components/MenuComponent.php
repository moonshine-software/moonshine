<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Components;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Leeto\MoonShine\Menu\Menu;

class MenuComponent extends Component
{
	public function render(): View|Factory|Htmlable|Closure|string|Application
	{
		$data = app(Menu::class)->all();

		return view('moonshine::components.menu', [
			"data" => $data,
		]);
	}
}
