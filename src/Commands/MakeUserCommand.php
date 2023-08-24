<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Support\Facades\Hash;
use MoonShine\MoonShineAuth;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:user {--u|username=} {--N|name=} {--p|password=}';

    protected $description = 'Create user';

    public function handle(): void
    {
        $username = $this->option('username') ?? text(
            'Username(' . config(
                'moonshine.auth.fields.username',
                'email'
            ) . ')',
            required: true
        );

        $name = $this->option('name') ?? text('Name', default: $username);
        $password = $this->option('password') ?? password('Password');

        if ($username && $name && $password) {
            MoonShineAuth::model()->query()->create([
                config('moonshine.auth.fields.username', 'email') => $username,
                config('moonshine.auth.fields.name', 'name') => $name,
                config(
                    'moonshine.auth.fields.password',
                    'password'
                ) => Hash::make($password),
            ]);

            $this->components->info('User is created');
        } else {
            $this->components->error('All params is required');
        }
    }
}
