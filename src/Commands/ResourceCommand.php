<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Commands;

class ResourceCommand extends MoonShineCommand
{
	protected $signature = 'moonshine:resource {name?} {--m|model=} {--t|title=}';

	protected $description = 'Create resource';

	public function handle(): void
	{
		$this->createResource();
	}

	public function createResource(): void
	{
		$name = str($this->argument('name'));

		if (! $name) {
			$name = str($this->ask('Resource name'));
		}

		$name = $name->ucfirst()
			->replace(['resource', 'Resource'], '');

		$model = $this->option('model') ?? $name;
		$title = $this->option('title') ?? $name;

		$resource = $this->getDirectory() . "/Resources/{$name}Resource.php";
		$contents = $this->getStub('Resource');
		$contents = str_replace('DummyModel', $model, $contents);
		$contents = str_replace('DummyTitle', $title, $contents);

		$this->laravel['files']->put(
			$resource,
			str_replace('Dummy', $name, $contents)
		);

		$this->info("{$name}Resource file was created: " . str_replace(base_path(), '', $resource));

		$this->comment('Now register resource');
	}
}
