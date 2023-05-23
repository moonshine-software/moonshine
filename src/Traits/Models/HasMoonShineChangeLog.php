<?php

declare(strict_types=1);

namespace MoonShine\Traits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MoonShine\Models\MoonshineChangeLog;
use MoonShine\Models\MoonshineUser;
use MoonShine\MoonShineAuth;

trait HasMoonShineChangeLog
{
    public static function bootHasMoonShineChangeLog(): void
    {
        static::created(static function (Model $row) {
            $row->createLog();
        });

        static::updated(static function (Model $row) {
            $row->createLog();
        });
    }

    public function createLog(): void
    {
        if (MoonShineAuth::guard()->check() && request()->user() instanceof MoonshineUser) {
            $this->changeLogs()->create([
                'moonshine_user_id' => MoonShineAuth::guard()->id(),
                'states_before' => $this->getOriginal(),
                'states_after' => $this->getChanges(),
            ]);
        }
    }

    public function changeLogs(): MorphMany
    {
        return $this->morphMany(
            MoonshineChangeLog::class,
            'changelogable'
        )->orderByDesc('created_at');
    }
}
