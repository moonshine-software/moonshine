<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Leeto\MoonShine\Contracts\RenderableContract;

class DashboardBlock
{
	protected array $items = [];

	public static function make(...$arguments): static
	{
		return new static(...$arguments);
	}

	final public function __construct(array $items = [])
	{
		$this->setItems($items);
	}

	/**
	 * @return array
	 */
	public function items(): array
	{
		return $this->items;
	}

	/**
	 * @param array $items
	 */
	public function setItems(array $items): void
	{
		$this->items = $items;
	}

	public function render(RenderableContract $item): Factory|View|Application
	{
		return view($item->getView(), [
			'block' => $this,
			'item' => $item,
		]);
	}
}
