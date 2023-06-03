<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithIsNowOnRoute
{
    public function isNowOnCreateForm(): bool
    {
        return request()?->routeIs('*.create', '*.store', '*.relation-field-form');
    }

    public function isNowOnUpdateForm(): bool
    {
        return request()?->routeIs('*.edit', '*.update', '*.relation-field-form');
    }

    public function isNowOnIndex(): bool
    {
        return request()?->routeIs('*.query-tag', '*.index', '*.relation-field-items');
    }

    public function isNowOnDetail(): bool
    {
        return request()?->routeIs('*.show');
    }

    public function isNowOnForm(): bool
    {
        return $this->isNowOnCreateForm()
            || $this->isNowOnUpdateForm();
    }
}
