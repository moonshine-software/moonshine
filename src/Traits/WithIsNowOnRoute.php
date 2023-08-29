<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithIsNowOnRoute
{
    public function isNowOnIndex(): bool
    {
        return request()?->route('pageUri') === 'index-page'
            || request('pageUri') === 'index-page';
    }

    public function isNowOnDetail(): bool
    {
        return request()?->route('pageUri') === 'show-page'
            || request('pageUri') === 'show-page';
    }

    public function isNowOnForm(): bool
    {
        return $this->isNowOnCreateForm()
            || $this->isNowOnUpdateForm();
    }

    public function isNowOnCreateForm(): bool
    {
        return (
            is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') === 'form-page'
        ) || (
            is_null(request('resourceItem'))
            && request('pageUri') === 'form-page'
        );
    }

    public function isNowOnUpdateForm(): bool
    {
        return (
            ! is_null(request()?->route('resourceItem'))
            && request()?->route('pageUri') === 'form-page'
        ) || (
            ! is_null(request('resourceItem'))
            && request('pageUri') === 'form-page'
        );
    }
}
