<?php
declare(strict_types=1);

namespace Leeto\MoonShine\Commands;

use Leeto\MoonShine\Models\MoonshineUser;

class UserCommand extends MoonShineCommand
{
	protected $signature = 'moonshine:user';

	protected $description = 'Create user';

	public function handle(): void
	{
		$email = $this->ask('Email');
		$name = $this->ask('Name');
		$password = $this->ask('Password');

		if ($email && $name && $password) {
			MoonshineUser::query()->create([
				'email' => $email,
				'name' => $name,
				'password' => bcrypt($password),
			]);

			$this->info('User is created');
		} else {
			$this->error('All params is required');
		}
	}
}
