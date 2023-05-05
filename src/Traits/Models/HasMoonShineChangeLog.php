<?php

declare(strict_types=1);

namespace MoonShine\Traits\Models;

use MoonShine\MoonShineAuth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use MoonShine\Models\MoonshineChangeLog;

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
        if (MoonShineAuth::instance()->check()) {
            $this->changeLogs()->create([
                'moonshine_user_id' => MoonShineAuth::instance()->id(),
                'states_before' => $this->getOriginal(),
                'states_after' => $this->getChanges(),
            ]);
        }
    }

    public function changeLogs(): MorphMany
    {
        return $this->morphMany(MoonshineChangeLog::class, 'changelogable')
            ->where(['moonshine_user_id' => MoonShineAuth::instance()->id()])
            ->orderByDesc('created_at');
    }
}
