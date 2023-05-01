<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Support\Facades\Hash;
use MoonShine\Models\MoonshineUser;

class UserCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:user';

    protected $description = 'Create user';

    public function handle(): void
    {
        $email = $this->ask('Email');
        $name = $this->ask('Name');
        $password = $this->secret('Password');

        if ($email && $name && $password) {
            MoonshineUser::query()->create([
                'email' => $email,
                'name' => $name,
                'password' => Hash::make($password),
            ]);

            $this->components->info('User is created');
        } else {
            $this->components->error('All params is required');
        }
    }
}
