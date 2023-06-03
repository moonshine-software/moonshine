<?php

use MoonShine\Commands\InstallCommand;

use function Pest\Laravel\artisan;

use Symfony\Component\Console\Command\Command;

uses()->group('commands');

it('reports progress', function (): void {
    artisan(InstallCommand::class)
        ->expectsOutputToContain('MoonShine installation ...')
        ->expectsOutputToContain('Installation completed')
        ->expectsOutputToContain("Now run 'php artisan moonshine:user'")
        ->assertExitCode(Command::SUCCESS);
});
