<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Models;

use function auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Leeto\MoonShine\Models\MoonshineChangeLog;

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
