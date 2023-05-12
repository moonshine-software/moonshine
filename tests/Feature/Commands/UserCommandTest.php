<?php

use MoonShine\Commands\UserCommand;

use function Pest\Laravel\artisan;

use Symfony\Component\Console\Command\Command;

uses()->group('commands');

it('reports progress', function () {
    artisan(UserCommand::class)
        ->expectsQuestion(
            'Username('.config('moonshine.auth.fields.username', 'email').')',
            'example@example.com'
        )
        ->expectsQuestion('Name', 'Admin')
        ->expectsQuestion('Password', 'example')
        ->expectsOutputToContain('User is created')
        ->assertExitCode(Command::SUCCESS);
});
