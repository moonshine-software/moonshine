<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Support\Facades\Hash;
use MoonShine\MoonShineAuth;

class MakeUserCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:user';

    protected $description = 'Create user';

    public function handle(): void
    {
        $username = $this->ask(
            'Username(' . config(
                'moonshine.auth.fields.username',
                'email'
            ) . ')'
        );
        $name = $this->ask('Name');
        $password = $this->secret('Password');

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
