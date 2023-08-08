<?php

use MoonShine\Commands\MakeUserCommand;

use function Pest\Laravel\artisan;

use Symfony\Component\Console\Command\Command;

uses()->group('commands');

it('reports progress', function (): void {
    artisan(MakeUserCommand::class)
        ->expectsQuestion(
            'Username(' . config('moonshine.auth.fields.username', 'email') . ')',
            'example@example.com'
        )
        ->expectsQuestion('Name', 'Admin')
        ->expectsQuestion('Password', 'example')
        ->expectsOutputToContain('User is created')
        ->assertExitCode(Command::SUCCESS);
});
