<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Filesystem\Filesystem;

use MoonShine\Laravel\DependencyInjection\MoonShine;
use function Laravel\Prompts\{confirm, info, multiselect};
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:publish')]
class PublishCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:publish {type?}';

    public function handle(): int
    {
        $types = $this->argument('type') ? [$this->argument('type')] : multiselect(
            'Types',
            [
                'assets' => 'Assets',
                'assets-template' => 'Assets template',
                'resources' => 'System Resources (MoonShineUserResource, MoonShineUserRoleResource)',
            ],
            required: true
        );

        if (in_array('assets', $types, true)) {
            $this->call('vendor:publish', [
                '--tag' => 'moonshine-assets',
                '--force' => true,
            ]);
        }

        if (in_array('assets-template', $types, true)) {
            $this->copyStub(
                'assets/css',
                resource_path('css/app.css')
            );

            $this->copyStub(
                'assets/postcss.config.preset',
                base_path('postcss.config.js')
            );

            $this->copyStub(
                'assets/tailwind.config.preset',
                base_path('tailwind.config.js')
            );

            if (confirm('Install modules automatically? (tailwindcss, autoprefixer, postcss)')) {
                $this->flushNodeModules();

                self::updateNodePackages(static fn ($packages) => [
                        '@tailwindcss/typography' => '^0.5',
                        '@tailwindcss/line-clamp' => '^0.4',
                        '@tailwindcss/aspect-ratio' => '^0.4',
                        'tailwindcss' => '^3',
                        'autoprefixer' => '^10',
                        'postcss' => '^8',
                    ] + $packages);

                $this->installNodePackages();

                info('Node packages installed');
            }

            info('App.css, postcss/tailwind.config published');
            info("Don't forget to add to MoonShineServiceProvider `Vite::asset('resources/css/app.css')`");
        }

        if (in_array('resources', $types, true)) {
            $this->publishSystemResource('MoonShineUserResource', 'MoonshineUser');
            $this->publishSystemResource('MoonShineUserRoleResource', 'MoonshineUserRole');

            info('Resources published');
        }

        return self::SUCCESS;
    }

    private function publishSystemResource(string $name, string $model): void
    {
        $classPath = "src/Resources/$name.php";
        $fullClassPath = moonshineConfig()->getDir("/Resources/$name.php");
        $targetNamespace = moonshineConfig()->getNamespace('\Resources');

        (new Filesystem())->put(
            $fullClassPath,
            file_get_contents(MoonShine::path($classPath))
        );

        $this->replaceInFile(
            'namespace MoonShine\Laravel\Resources;',
            "namespace $targetNamespace;",
            $fullClassPath
        );

        $this->replaceInFile(
            "use MoonShine\Laravel\Models\\$model;",
            "use MoonShine\Laravel\Models\\$model;\nuse MoonShine\Laravel\Resources\ModelResource;",
            $fullClassPath
        );

        $this->replaceInFile(
            "use MoonShine\Laravel\Resources\\$name;",
            "use $targetNamespace\\$name;",
            app_path('Providers/MoonShineServiceProvider.php')
        );
    }
}
