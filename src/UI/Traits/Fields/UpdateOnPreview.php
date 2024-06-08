<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Sets\UpdateOnPreviewPopover;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

    protected bool $updateOnPreviewPopover = true;

    protected ?string $updateOnPreviewParentComponent = null;

    protected ?Closure $updateOnPreviewUrl = null;

    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->updateOnPreview(condition: false);

        return parent::readonly($condition);
    }

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

        $this->updateOnPreviewParentComponent = $component;

        return $this->setUpdateOnPreviewUrl(
            $this->updateOnPreviewUrl,
            events: [
                AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, "$component-{row-id}"),
            ]
        );
    }

    public function updateInPopover(
        string $component
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

        $this->updateOnPreviewParentComponent = $component;
        $this->updateOnPreviewPopover = true;

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
            $url ?? static fn (?CastedData $data, mixed $value, Field $field): ?string => $data->getKey() ? moonshineRouter()->getEndpoints()->updateColumn(
                resource: $field->getNowOnResource(),
                extra: [
                    'resourceItem' => $data->getKey(),
                    'relation' => data_get($field->getNowOnQueryParams(), 'relation'),
                ],
            ) : null,
            $events
        );
    }

    /**
     * @param  Closure(mixed $data, mixed $value, self $field): string  $url
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

        if($this->updateOnPreviewPopover && $this->updateOnPreviewParentComponent && $this->isPreviewMode()) {
            return (string) call_user_func(
                new UpdateOnPreviewPopover(field: $this, component: $this->updateOnPreviewParentComponent)
            );
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
