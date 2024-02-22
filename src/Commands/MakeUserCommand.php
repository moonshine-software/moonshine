<?php

declare(strict_types=1);

namespace MoonShine\Commands;

use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\{error, info, password, text};

use MoonShine\MoonShineAuth;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'moonshine:user')]
class MakeUserCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:user {--u|username=} {--N|name=} {--p|password=}';

    protected $description = 'Create user';

    public function handle(): int
    {
        $username = $this->option('username') ?? text(
            'Username(' . config(
                'moonshine.auth.fields.username',
                'email'
            ) . ')',
            required: true,
            validate: fn (string $value): ?string => $this->validateUserEmail($value)
        );

        $emailError = $this->validateUserEmail($username);

        if ($emailError) {
            $this->error($emailError);
            return MoonShineCommand::FAILURE;
        }

        $name = $this->option('name') ?? text('Name', default: $username, required: true);
        $password = $this->option('password') ?? password('Password', required: true);

        if ($username && $name && $password) {
            MoonShineAuth::model()->query()->create([
                config('moonshine.auth.fields.username', 'email') => $username,
                config('moonshine.auth.fields.name', 'name') => $name,
                config(
                    'moonshine.auth.fields.password',
                    'password'
                ) => Hash::make($password),
            ]);

            info('User is created');
        } else {
            error('All params is required');
        }

        return self::SUCCESS;
    }

    private function validateUserEmail(string $email): string|null
    {
        $isAlreadyExist = MoonShineAuth::model()->query()->where(config('moonshine.auth.fields.username', 'email'), $email)->exists();

        return $isAlreadyExist ? "User with email $email already exist" : null;
    }
}
