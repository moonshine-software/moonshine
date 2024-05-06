<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Text;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Condition;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

    protected ?Closure $updateOnPreviewUrl = null;

    protected ?Closure $updateOnPreviewCustomUrl = null;

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
    public function withUpdateRow(
        string $component,
    ): static {
        if ($this->isRawMode()) {
            return $this;
        }

        if (is_null($this->updateOnPreviewUrl)) {
            $this->updateOnPreview();
        }

        if (is_null($this->updateOnPreviewUrl)) {
            return $this;
        }

        return $this->onChangeUrl(
            $this->updateOnPreviewUrl,
            events: [
                AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, "$component-{row-id}"),
            ]
        );
    }

    /**
     * @throws FieldException
     */
    public function updateOnPreview(
        ?Closure $url = null,
        ?ResourceContract $resource = null,
        mixed $condition = null,
        array $events = [],
    ): static {
        if ($this->isRawMode() || (app()->runningInConsole() && ! app()->runningUnitTests())) {
            return $this;
        }

        $this->updateOnPreview = Condition::boolean($condition, true);

        if (! $this->updateOnPreview) {
            return $this;
        }

        $this->updateOnPreviewCustomUrl = $url;

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
            $this->getUpdateOnPreviewCustomUrl() ?? $this->getDefaultUpdateRoute(),
            $events
        );
    }

    public function setUpdateOnPreviewUrl(Closure $url, array $events = []): static
    {
        $this->updateOnPreviewUrl = $url;

        return $this->onChangeUrl(
            $this->updateOnPreviewUrl,
            events: $events
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

    public function hasUpdateOnPreviewCustomUrl(): bool
    {
        return ! is_null($this->updateOnPreviewCustomUrl);
    }

    public function getUpdateOnPreviewCustomUrl(): ?Closure
    {
        return $this->updateOnPreviewCustomUrl;
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
