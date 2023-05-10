<?php

use MoonShine\Commands\ResourceCommand;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

uses()->group('commands');

it('reports progress', function () {
    artisan(ResourceCommand::class)
        ->expectsQuestion('Name', 'Test')
        ->expectsOutputToContain('Now register resource in menu')
        ->assertExitCode(Command::SUCCESS);
});

it('reports progress singleton', function () {
    artisan(ResourceCommand::class, ['--singleton' => true])
        ->expectsQuestion('Name', 'Test')
        ->expectsQuestion('Item id', 1)
        ->expectsOutputToContain('Now register resource in menu')
        ->assertExitCode(Command::SUCCESS);
});
