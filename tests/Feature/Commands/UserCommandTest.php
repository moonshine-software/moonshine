<?php

use MoonShine\Commands\UserCommand;

use function Pest\Laravel\artisan;

use Symfony\Component\Console\Command\Command;

uses()->group('commands');

it('reports progress', function () {
    artisan(UserCommand::class)
        ->expectsQuestion('Email', 'example@example.com')
        ->expectsQuestion('Name', 'Admin')
        ->expectsQuestion('Password', 'example')
        ->expectsOutputToContain('User is created')
        ->assertExitCode(Command::SUCCESS);
});
