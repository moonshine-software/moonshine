<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Filesystem\Filesystem;

use function Laravel\Prompts\{confirm, info, multiselect, note};

use MoonShine\MoonShine;
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
                'layout' => 'Layout',
                'favicons' => 'Favicons',
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

        if (in_array('layout', $types, true)) {
            $this->copyStub(
                'Layout',
                MoonShine::dir('/MoonShineLayout.php'),
                [
                    '{namespace}' => MoonShine::namespace(),
                ]
            );

            $this->replaceInFile(
                'use MoonShine\MoonShineLayout;',
                'use App\MoonShine\MoonShineLayout;',
                config_path('moonshine.php')
            );

            info('Layout published');
        }

        if (in_array('favicons', $types, true)) {
            $this->makeDir(resource_path('views/vendor/moonshine/layouts/shared'));

            $this->copyStub(
                'favicon',
                resource_path('views/vendor/moonshine/layouts/shared/favicon.blade.php')
            );

            info('Favicons published');

            note(sprintf(
                'Creating file [%s]',
                resource_path('views/vendor/moonshine/layouts/shared/favicon.blade.php'),
            ));
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
        $classPath = "/src/Resources/$name.php";
        $fullClassPath = MoonShine::dir("/Resources/$name.php");
        $targetNamespace = MoonShine::namespace('\Resources');

        (new Filesystem())->put(
            $fullClassPath,
            file_get_contents(MoonShine::path($classPath))
        );

        $this->replaceInFile(
            'namespace MoonShine\Resources;',
            "namespace $targetNamespace;",
            $fullClassPath
        );

        $this->replaceInFile(
            "use MoonShine\Models\\$model;",
            "use MoonShine\Models\\$model;\nuse MoonShine\Resources\ModelResource;",
            $fullClassPath
        );

        $this->replaceInFile(
            "use MoonShine\Resources\\$name;",
            "use $targetNamespace\\$name;",
            app_path('Providers/MoonShineServiceProvider.php')
        );
    }
}
