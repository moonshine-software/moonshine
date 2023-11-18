<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait HasUpdateAsync
{
    protected bool $isUpdateAsync = false;

    protected string $updateAsyncUrl = '';

    protected function prepareUpdateAsyncUrl(?string $updateAsyncUrl = null): ?string
    {
        return $updateAsyncUrl;
    }

    public function updateAsync(?string $asyncUpdateUrl = null): static
    {
        $this->updateAsyncUrl = $asyncUpdateUrl ?? $this->prepareUpdateAsyncUrl($asyncUpdateUrl);
        $this->isUpdateAsync = true;
        return $this;
    }

    protected function isUpdateAsync(): bool
    {
        return $this->isUpdateAsync;
    }

    protected function updateAsyncUrl(): string
    {
        return $this->updateAsyncUrl;
    }
}