<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\MoonShineRouter;
use MoonShine\Support\Condition;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

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

    public function updateOnPreview(
        ?ResourceContract $resource = null,
        ?Closure $url = null,
        mixed $condition = null
    ): static {
        $this->updateOnPreviewUrl = $resource ? $this->getDefaultUpdateRoute($resource) : $url;
        $this->updateOnPreview = Condition::boolean($condition, true);

        return $this;
    }

    protected function getDefaultUpdateRoute(ResourceContract $resource): Closure
    {
        return fn ($item): string => MoonShineRouter::to('resource.update-column', [
            'resourceItem' => $item->getKey(),
            'resourceUri' => $resource->uriKey(),
        ]);
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

    public function preview(): View|string
    {
        if (! $this->isUpdateOnPreview() || $this->isRawMode()) {
            return parent::preview();
        }

        return view($this->getView(), [
            'element' => $this,
            'updateOnPreview' => $this->isUpdateOnPreview(),
        ])->render();
    }
}
