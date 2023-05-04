<?php

use MoonShine\Commands\InstallCommand;
use Symfony\Component\Console\Command\Command;
use function Pest\Laravel\artisan;

uses()->group('commands');

it('reports progress', function() {
    artisan(InstallCommand::class)
        ->expectsOutputToContain('MoonShine installation ...')
        ->expectsOutputToContain('Installation completed')
        ->expectsOutputToContain("Now run 'php artisan moonshine:user'")
        ->assertExitCode(Command::SUCCESS);
});
