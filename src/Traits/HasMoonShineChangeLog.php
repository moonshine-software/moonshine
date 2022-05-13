<?php

namespace Leeto\MoonShine\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Leeto\MoonShine\Models\MoonshineChangeLog;

trait HasMoonShineChangeLog
{
    public static function boot()
    {
        parent::boot();

        static::created(function($row){
            $row->createLog();
        });

        static::updated(function($row){
            $row->createLog();
        });
    }

    public function createLog()
    {
        if(auth(config('moonshine.auth.guard'))->check()) {
            $this->changeLogs()
                ->create([
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