<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\{error, info, password, text};

use MoonShine\Laravel\MoonShineAuth;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:user')]
class MakeUserCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:user {--u|username=} {--N|name=} {--p|password=}';

    protected $description = 'Create user';

    public function handle(): int
    {
        $username = $this->uniqueUsername();

        $name = $this->option('name') ?? text('Name', default: $username);
        $password = $this->option('password') ?? password('Password');

        if ($username && $name && $password) {
            MoonShineAuth::getModel()->query()->create([
                moonshineConfig()->getUserField('username', 'email') => $username,
                moonshineConfig()->getUserField('name') => $name,
                moonshineConfig()->getUserField('password') => Hash::make($password),
            ]);

            info('User is created');
        } else {
            error('All params is required');
        }

        return self::SUCCESS;
    }

    private function uniqueUsername(): string
    {
        $username = $this->option('username');

        while (true) {
            $username ??= text(
                'Username(' . moonshineConfig()->getUserField(
                    'username',
                    'email'
                ) . ')',
                required: true
            );

            $exists = MoonShineAuth::getModel()
                ->query()
                ->where(
                    moonshineConfig()->getUserField('username', 'email'),
                    $username,
                )
                ->exists();

            if(! $exists) {
                break;
            }

            $this->components->warn('There is already a username, try another one');
            $username = null;
        }

        return $username;
    }
}
