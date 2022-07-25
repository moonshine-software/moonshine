<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Leeto\MoonShine\Traits\Fields\SearchableTrait;

class Select extends Field
{
	use SearchableTrait;

	protected static string $view = 'select';

	public function indexViewValue(Model $item, bool $container = true): string
	{
		if (isset($this->values()[$item->{$this->field()}])) {
			return $this->values()[$item->{$this->field()}];
		}

		return parent::indexViewValue($item, $container);
	}
}
