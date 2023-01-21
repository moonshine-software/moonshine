<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoonshineUserPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'moonshine_user_id',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'collection'
    ];

    public function moonshineUser(): BelongsTo
    {
        return $this->belongsTo(MoonshineUser::class);
    }
}
