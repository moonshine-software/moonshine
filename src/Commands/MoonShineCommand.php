<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;
use Leeto\MoonShine\MoonShine;

class MoonShineCommand extends Command
{
	protected function getStub(string $name): string
	{
		return $this->laravel['files']->get(MoonShine::path("/stubs/$name.stub"));
	}

	protected function getDirectory(): string
	{
		return MoonShine::DIR;
	}

	protected function makeDir(string $path = ''): void
	{
		if (isset($this->laravel['files'])) {
			$this->laravel['files']->makeDirectory("{$this->getDirectory()}/$path", 0755, true, true);
		}
	}
}
