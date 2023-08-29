<?php

declare(strict_types=1);

namespace MoonShine\Tests\Fixtures\Requests;

use MoonShine\Models\MoonshineUserRole;

final class CrudRequestFactory
{
    protected string $name = 'Danil Shutsky';

    protected int $moonshine_user_role_id = MoonshineUserRole::DEFAULT_ROLE_ID;

    protected string $email = 'example@example.com';

    protected string $password = '123456';
    protected string $password_repeat = '123456';

    public static function new(): self
    {
        return new self();
    }

    public function withName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    public function withEmail(string $value): self
    {
        $this->email = $value;

        return $this;
    }

    public function withPassword(string $value): self
    {
        $this->password = $value;

        return $this;
    }

    public function withPasswordRepeat(string $value): self
    {
        $this->password_repeat = $value;

        return $this;
    }

    public function create(array $extra = []): array
    {
        return $extra + [
            'name' => $this->name,
            'moonshine_user_role_id' => $this->moonshine_user_role_id,
            'email' => $this->email,
            'password' => $this->password,
            'password_repeat' => $this->password_repeat,
        ];
    }
}
