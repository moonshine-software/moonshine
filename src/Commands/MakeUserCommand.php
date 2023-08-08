<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;

use function Laravel\Prompts\text;

use MoonShine\MoonShineAuth;

class MakeUserCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:user';

    protected $description = 'Create user';

    public function handle(): void
    {
        $username = text(
            'Username(' . config(
                'moonshine.auth.fields.username',
                'email'
            ) . ')',
            required: true
        );

        $name = text('Name', default: $username);
        $password = password('Password');

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
