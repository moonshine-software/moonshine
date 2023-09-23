<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use function Laravel\Prompts\{info, multiselect};

use MoonShine\MoonShine;

class PublishCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:publish';

    public function handle(): int
    {
        $types = multiselect(
            'Types',
            [
                'assets' => 'Assets',
                'layout' => 'Layout',
            ],
            required: true
        );

        if (in_array('assets', $types, true)) {
            $this->call('vendor:publish', [
                '--tag' => 'moonshine-assets',
            ]);
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

        return self::SUCCESS;
    }
}
