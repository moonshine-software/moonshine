<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Leeto\MoonShine\Models\MoonshineChangeLog;

use function auth;

trait HasMoonShineChangeLog
{
    public static function bootHasMoonShineChangeLog(): void
    {
        static::created(static function (MoonshineChangeLog $row) {
            $row->createLog();
        });

        static::updated(static function (MoonshineChangeLog $row) {
            $row->createLog();
        });
    }

    public function createLog(): void
    {
        if (auth(config('moonshine.auth.guard'))->check()) {
            $this->changeLogs()->create([
                'moonshine_user_id' => auth(config('moonshine.auth.guard'))->id(),
                'states_before' => $this->getOriginal(),
                'states_after' => $this->getChanges(),
            ]);
        }
    }

    public function changeLogs(): MorphMany
    {
        return $this->morphMany(MoonshineChangeLog::class, 'changelogable')
            ->where(['moonshine_user_id' => auth(config('moonshine.auth.guard'))->id()])
            ->orderByDesc('created_at');
    }
}
