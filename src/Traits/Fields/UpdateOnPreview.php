<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Text;
use MoonShine\MoonShineRouter;
use MoonShine\Support\Condition;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

    protected mixed $updateOnPreviewData = null;

    protected ?Closure $updateOnPreviewUrl = null;

    protected ?Closure $url = null;

    protected ?string $updateColumnResourceUri = null;

    protected ?string $updateColumnPageUri = null;

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
        ?Closure $url = null,
        ?ResourceContract $resource = null,
        mixed $condition = null
    ): static {
        $this->updateOnPreview = Condition::boolean($condition, true);

        if (! $this->updateOnPreview) {
            return $this;
        }

        $this->url = $url;

        $resource ??= moonshineRequest()->getResource();

        if (is_null($resource) && is_null($url)) {
            throw new FieldException('updateOnPreview must accept either $resource or $url parameters');
        }

        if (! is_null($resource)) {
            if (is_null($resource->formPage())) {
                throw new FieldException('To use the updateOnPreview method, you must set FormPage to the Resource');
            }

            $this->updateColumnResourceUri = $resource->uriKey();
            $this->updateColumnPageUri = $resource->formPage()->uriKey();
        }

        if ($this instanceof Text &&  ! $this->isNowOnForm()) {
            $this->locked();
        }

        $this->updateOnPreviewUrl = $this->getUrl() ?? $this->getDefaultUpdateRoute();

        return $this;
    }

    protected function getDefaultUpdateRoute(): Closure
    {
        return fn ($item): string => MoonShineRouter::to('column.resource.update-column', [
            'resourceItem' => $item->getKey(),
            'resourceUri' => $this->getResourceUriForUpdate(),
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

    public function setUpdateOnPreviewUrl(Closure $url): static
    {
        $this->updateOnPreviewUrl = $url;

        return $this;
    }

    public function getUrl(): ?Closure
    {
        return $this->url;
    }

    public function getResourceUriForUpdate(): ?string
    {
        return $this->updateColumnResourceUri;
    }

    public function getPageUriForUpdate(): ?string
    {
        return $this->updateColumnPageUri;
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
