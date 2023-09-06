<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use MoonShine\Support\Condition;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = true;

    protected mixed $updateOnPreviewData = null;

    protected ?Closure $updateOnPreviewUrl = null;

    protected function prepareFill(array $raw = [], mixed $casted = null): mixed
    {
        $this->updateOnPreviewData = $casted ?? $raw;

        return parent::prepareFill($raw, $casted);
    }

    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->updateOnPreview(condition: false);

        return parent::readonly($condition);
    }

    public function updateOnPreview(?Closure $url = null, mixed $condition = null): static
    {
        $this->updateOnPreviewUrl = $url;
        $this->updateOnPreview = Condition::boolean($condition, true);

        return $this;
    }

    public function isUpdateOnPreview(): bool
    {
        return $this->updateOnPreview;
    }

    public function getUpdateOnPreviewUrl(): string
    {
        return is_closure($this->updateOnPreviewUrl)
            ? ($this->updateOnPreviewUrl)($this->updateOnPreviewData)
            : '';
    }

    protected function resolvePreview(): string
    {
        if (! $this->isUpdateOnPreview() && $this->isRawMode()) {
            return parent::resolvePreview();
        }

        return view($this->getView(), [
            'element' => $this,
            'updateOnPreview' => $this->isUpdateOnPreview(),
        ])->render();
    }
}
