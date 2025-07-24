<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Sets\UpdateOnPreviewPopover;

trait UpdateOnPreview
{
    protected bool $updateOnPreview = false;

    protected bool $updateOnPreviewPopover = false;

    protected ?string $updateOnPreviewParentComponent = null;

    protected ?Closure $updateOnPreviewUrl = null;

    public function readonly(Closure|bool|null $condition = null): static
    {
        $this->updateOnPreview(condition: false);
        $this->updateOnPreviewPopover = false;

        return parent::readonly($condition);
    }

    public function disableUpdateOnPreview(): static
    {
        $this->updateOnPreview = false;
        $this->onChangeUrl = null;
        $this->updateOnPreviewPopover = false;
        $this->updateOnPreviewUrl = null;

        $this->removeAttribute('@change');

        return $this;
    }

    public function withUpdateRow(string $component): static
    {

        if ($this->isRawMode()) {
            return $this;
        }

        if (\is_null($this->updateOnPreviewUrl)) {
            $this->updateOnPreview();
        }

        if (\is_null($this->updateOnPreviewUrl)) {
            return $this;
        }

        $this->updateOnPreviewParentComponent = $component;

        return $this->setUpdateOnPreviewUrl(
            $this->updateOnPreviewUrl,
            events: [
                AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, $component),
            ]
        );
    }

    public function updateInPopover(string $component): static
    {
        $this->updateOnPreviewPopover = true;

        return $this->withUpdateRow($component);
    }

    /**
     * @param  ?Closure(mixed $data, mixed $value, static $field): string  $url
     * @param string[] $events
     */
    public function updateOnPreview(
        ?Closure $url = null,
        ?ResourceContract $resource = null,
        Closure|bool|null $condition = null,
        array $events = [],
    ): static {
        $this->updateOnPreview = value($condition, $this) ?? true;

        if (! $this->updateOnPreview) {
            return $this;
        }

        if (! \is_null($resource)) {
            $this->nowOn(
                page: $resource instanceof CrudResourceContract ? $resource->getFormPage() : null,
                resource: $resource
            );
        }

        $router = $this->getCore()->getRouter();

        return $this->setUpdateOnPreviewUrl(
            $url ?? static fn (?DataWrapperContract $data, mixed $value, FieldContract $field): ?string => $data?->getKey() ? $router->getEndpoints()->updateField(
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
     * @param  Closure(mixed $data, mixed $value, FieldContract $field): string  $url
     * @param string[] $events
     */
    public function setUpdateOnPreviewUrl(Closure $url, array $events = []): static
    {
        $this->updateOnPreviewUrl = $url;

        return $this->onChangeUrl(
            $this->updateOnPreviewUrl,
            method: HttpMethod::PUT,
            events: $events
        );
    }

    public function isUpdateOnPreview(): bool
    {
        return $this->updateOnPreview;
    }

    protected function isOnChangeCondition(): bool
    {
        if (! \is_null($this->onChangeUrl) && ! $this->isUpdateOnPreview()) {
            return true;
        }

        return $this->isUpdateOnPreview() && \is_null($this->getFormName());
    }

    protected function resolveRender(): Renderable|Closure|string
    {
        if ($this->isUpdateOnPreview() && $this->isPreviewMode()) {
            $this->defaultMode();
        }

        if ($this->updateOnPreviewPopover && $this->updateOnPreviewParentComponent && $this->isPreviewMode()) {
            $label = (string) $this->getCore()->getTranslator()->get('moonshine::ui.save');

            return (string) \call_user_func(
                new UpdateOnPreviewPopover(
                    field: $this,
                    label: $label,
                    component: $this->updateOnPreviewParentComponent,
                    route: $this->getCore()->getRouter()->getEndpoints()->updateField(
                        extra: [
                            'resourceItem' => $this->getData()?->getKey(),
                        ]
                    )
                )
            );
        }

        return parent::resolveRender();
    }
}
