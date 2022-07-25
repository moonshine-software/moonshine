<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Fields;


class WYSIWYG extends Field
{
	protected static string $view = 'wysiwyg';

	protected array $assets = [
		'js' => ['vendor/moonshine/js/trix/trix.js'],
		'css' => ['vendor/moonshine/css/trix/trix.css'],
	];
}
