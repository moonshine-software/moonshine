<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;
use Leeto\MoonShine\Models\MoonshineUser;

class UserCommand extends BaseMoonShineCommand
{
    protected $signature = 'moonshine:user';

    protected $description = 'Create user';

    public function handle(): void
    {
        $email = $this->ask('Email');
        $name = $this->ask('Name');
        $password = $this->ask('Password');

        if($email && $name && $password) {
            MoonshineUser::query()->create([
                'email' => $email,
                'name' => $name,
                'password' => bcrypt($password)
            ]);

            $this->info('User is created');
        } else {
            $this->error('All params is required');
        }
    }
}
