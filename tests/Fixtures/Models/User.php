<?php

namespace MoonShine\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use MoonShine\Traits\Models\HasMoonShineChangeLog;
use Orchestra\Testbench\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;
    use HasMoonShineChangeLog;

    protected $table = 'users';

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
