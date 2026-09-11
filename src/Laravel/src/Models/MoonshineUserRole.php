<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MoonShine\Laravel\Database\Factories\MoonshineUserRoleFactory;

/**
 * @property string $name
 */
class MoonshineUserRole extends Model
{
    /**
     * @use HasFactory<MoonshineUserRoleFactory>
     */
    use HasFactory;

    final public const int DEFAULT_ROLE_ID = 1;

    protected $fillable = ['name'];

    /**
     * @return HasMany<MoonshineUser, $this>
     */
    public function moonshineUsers(): HasMany
    {
        return $this->hasMany(MoonshineUser::class);
    }
}
