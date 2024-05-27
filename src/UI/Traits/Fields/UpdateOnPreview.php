<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Exceptions\FieldException;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

    protected ?Closure $updateOnPreviewUrl = null;

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

        return $this->setUpdateOnPreviewUrl(
            $this->updateOnPreviewUrl,
            events: [
                AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, "$component-{row-id}"),
            ]
        );
    }

    public function updateOnPreview(
        ?Closure $url = null,
        ?ResourceContract $resource = null,
        mixed $condition = null,
        array $events = [],
    ): static {
        $this->updateOnPreview = value($condition, $this) ?? true;

        if (! $this->updateOnPreview) {
            return $this;
        }

        if(! is_null($resource)) {
            $this->nowOn(page: $resource->formPage(), resource: $resource);
        }

        return $this->setUpdateOnPreviewUrl(
            $url ?? fn (Model $item, mixed $value, Field $field): ?string => $item->exists ? moonshineRouter()->updateColumn(
                resourceUri: $field->getNowOnResource() ? $field->getNowOnResource()->uriKey() : moonshineRequest()->getResourceUri(),
                resourceItem: $item->getKey(),
                relation: data_get($field->getNowOnQueryParams(), 'relation')
            ) : null,
            $events
        );
    }

    /**
     * @param  Closure(mixed $data, mixed $value, self $field): string  $url
     * @return $this
     */
    public function setUpdateOnPreviewUrl(Closure $url, array $events = []): static
    {
        $this->updateOnPreviewUrl = $url;

        return $this->onChangeUrl(
            $this->updateOnPreviewUrl,
            events: $events
        );
    }

    public function isUpdateOnPreview(): bool
    {
        return $this->updateOnPreview;
    }

    protected function onChangeCondition(): bool
    {
        if (! is_null($this->onChangeUrl) && ! $this->isUpdateOnPreview()) {
            return true;
        }

        return $this->isUpdateOnPreview() && is_null($this->getFormName());
    }

    protected function resolveRender(): View|Closure|string
    {
        if (! $this->isUpdateOnPreview() || $this->isRawMode()) {
            return parent::resolveRender();
        }

        if ($this instanceof Text && $this->isPreviewMode()) {
            $this->locked();
        }

        if ($this->getView() === '') {
            return $this->toValue();
        }

        return $this->renderView();
    }
}
