<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Resource;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait ResourcePolicy
{
    public static bool $withPolicy = false;

    public function policies(Model $item = null): array
    {
        return [
            'viewAny' => $this->can('viewAny'),
            'view' => $this->can('view', $item),
            'create' => $this->can('create'),
            'update' => $this->can('update', $item),
            'delete' => $this->can('delete', $item),
            'massDelete' => $this->can('massDelete'),
            'restore' => $this->can('restore', $item),
            'forceDelete' => $this->can('forceDelete', $item),
        ];
    }

    public function isWithPolicy(): bool
    {
        return static::$withPolicy;
    }

    public function can(string $ability, Model $item = null): bool
    {
        if (!$this->isWithPolicy()) {
            return true;
        }

        return Gate::forUser(auth('moonshine')->user())
            ->allows($ability, $item ?? $this->getModel());
    }
}
