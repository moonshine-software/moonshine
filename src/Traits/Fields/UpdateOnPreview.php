<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Text;
use MoonShine\Support\Condition;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

    protected ?Closure $updateOnPreviewUrl = null;

    protected ?Closure $url = null;

    protected ?string $updateColumnResourceUri = null;

    protected ?string $updateColumnPageUri = null;

    /**
     * @throws FieldException
     */
    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->updateOnPreview(condition: false);

        return parent::readonly($condition);
    }

    /**
     * @throws FieldException
     */
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

        return $this->setUpdateOnPreviewUrl(
            $this->getUrl() ?? $this->getDefaultUpdateRoute()
        );
    }

    public function setUpdateOnPreviewUrl(Closure $url): static
    {
        $this->updateOnPreviewUrl = $url;

        return $this->onChangeUrl(
            $this->updateOnPreviewUrl
        );
    }

    protected function getDefaultUpdateRoute(): Closure
    {
        return moonshineRouter()->updateColumn(
            $this->getResourceUriForUpdate()
        );
    }

    public function isUpdateOnPreview(): bool
    {
        return $this->updateOnPreview;
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

    protected function onChangeCondition(): bool
    {
        if (! is_null($this->onChangeUrl) && ! $this->isUpdateOnPreview()) {
            return true;
        }

        return $this->isUpdateOnPreview() && is_null($this->getFormName());
    }

    public function preview(): View|string
    {
        if (! $this->isUpdateOnPreview() || $this->isRawMode()) {
            return parent::preview();
        }

        $this->previewMode = true;

        if ($this instanceof Text) {
            $this->locked();
        }

        return $this->forcePreview()->render();
    }
}
