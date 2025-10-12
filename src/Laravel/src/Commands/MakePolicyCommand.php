<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;

use function Laravel\Prompts\{suggest};

use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Support\StubsPath;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'moonshine:policy')]
class MakePolicyCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:policy {className?} {--json} {--without-output}';

    protected $description = 'Create policy for Model';

    /**
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function handle(): int
    {
        $modelPath = is_dir(app_path('Models')) ? app_path('Models') : app_path();

        $className = $this->argument('className') ?? suggest(
            label: 'Model',
            options: Collection::make((new Finder())->files()->depth(0)->in($modelPath))
                ->map(static fn ($file) => $file->getBasename('.php'))
                ->values()
                ->all(),
            required: true,
        );

        $className = Str::of($className)
            ->ucfirst()
            ->remove('policy', false)
            ->value();

        $model = $this->qualifyModel($className);
        $className = class_basename($model) . 'Policy';

        $stubsPath = new StubsPath($className, 'php');

        $stubsPath->prependDir(
            'app/Policies',
        )->prependNamespace(
            'App\\Policies',
        );

        $this->makeDir($stubsPath->dir);

        $this->copyStub('Policy', $stubsPath->getPath(), [
            'DummyClass' => $stubsPath->name,
            '{model-namespace}' => $model,
            '{model}' => class_basename($model),
            '{user-model-namespace}' => MoonShineAuth::getModel()::class,
            '{user-model}' => class_basename(MoonShineAuth::getModel()),
        ]);

        $this->wasCreatedInfo($stubsPath);

        $this->formatOutput();

        return self::SUCCESS;
    }
}
