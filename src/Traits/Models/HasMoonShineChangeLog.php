<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Leeto\MoonShine\Models\MoonshineChangeLog;

use function auth;

trait HasMoonShineChangeLog
{
    public static function boot()
    {
        parent::boot();

        static::created(function ($row) {
            $row->createLog();
        });

        static::updated(function ($row) {
            $row->createLog();
        });
    }

    public function createLog()
    {
        if (auth('moonshine')->check()) {
            $this->changeLogs()
                ->create([
                    'moonshine_user_id' => auth('moonshine')->id(),
                    'states_before' => $this->getOriginal(),
                    'states_after' => $this->getChanges(),
                ]);
        }
    }

    public function changeLogs(): MorphMany
    {
        return $this->morphMany(MoonshineChangeLog::class, 'changelogable')
            ->where(['moonshine_user_id' => auth('moonshine')->id()])
            ->orderByDesc('created_at');
    }
}
