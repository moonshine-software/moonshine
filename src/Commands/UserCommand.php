<?php

namespace Leeto\MoonShine\Commands;

use Illuminate\Console\Command;
use Leeto\MoonShine\Models\MoonshineUser;

class UserCommand extends BaseMoonShineCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'moonshine:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user';


    public function handle()
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
