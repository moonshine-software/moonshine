<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

use function Laravel\Prompts\outro;
use function Laravel\Prompts\suggest;

use MoonShine\MoonShineAuth;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'moonshine:policy')]
class MakePolicyCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:policy {className?}';

    protected $description = 'Create policy for Model';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $modelPath = is_dir(app_path('Models')) ? app_path('Models') : app_path();

        $className = suggest(
            'Model',
            collect((new Finder())->files()->depth(0)->in($modelPath))
                ->map(fn ($file) => $file->getBasename('.php'))
                ->values()
                ->all()
        );

        $model = $this->qualifyModel($className);
        $className = class_basename($model) . "Policy";

        $path = app_path("/Policies/$className.php");

        if(! is_dir(app_path('/Policies'))) {
            $this->makeDir(app_path('/Policies'));
        }

        $this->copyStub('Policy', $path, [
            'DummyClass' => $className,
            '{model-namespace}' => $model,
            '{model}' => class_basename($model),
            '{user-model-namespace}' => MoonShineAuth::model()::class,
            '{user-model}' => class_basename(MoonShineAuth::model()),
        ]);

        outro(
            "$className was created: " . str_replace(
                base_path(),
                '',
                $path
            )
        );

        return self::SUCCESS;
    }
}
