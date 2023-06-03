<?php

use MoonShine\Commands\UserCommand;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

uses()->group('commands');

it('reports progress', function (): void {
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
