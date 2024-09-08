<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Url;
use MoonShine\UI\Traits\Fields\WithBadge;
use MoonShine\UI\Traits\Fields\WithHint;
use MoonShine\UI\Traits\Fields\WithLink;
use MoonShine\UI\Traits\Fields\WithSorts;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * The Field class complements the FormElement class with sugar and rendering logic
 *
 * @method static static make(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
 */
abstract class Field extends FormElement implements FieldContract
{
    use WithSorts;
    use WithHint;
    use WithLink;
    use WithBadge;

    protected bool $defaultMode = false;

    protected bool $previewMode = false;

    protected bool $rawMode = false;

    protected ?Closure $previewCallback = null;

    protected ?Closure $renderCallback = null;

    protected ?Closure $beforeRender = null;

    protected ?Closure $afterRender = null;

    protected bool $withWrapper = true;

    protected bool $isGroup = false;

    protected bool $columnSelection = true;

    protected bool $nullable = false;

    protected bool $isBeforeLabel = false;

    protected bool $isInsideLabel = false;

    protected ?Closure $onChangeUrl = null;

    public function defaultMode(): static
    {
        $this->defaultMode = true;

        return $this;
    }

    public function isDefaultMode(): bool
    {
        return $this->defaultMode;
    }

    public function previewMode(): static
    {
        $this->previewMode = true;

        return $this;
    }

    public function isPreviewMode(): bool
    {
        return $this->previewMode;
    }

    public function rawMode(Closure|bool|null $condition = null): static
    {
        $this->rawMode = value($condition, $this) ?? true;

        return $this;
    }

    public function isRawMode(): bool
    {
        return $this->rawMode;
    }

    /**
     * @param  Closure(mixed $value, static $field): mixed  $callback
     */
    public function changePreview(Closure $callback): static
    {
        $this->previewCallback = $callback;

        return $this;
    }

    public function isPreviewChanged(): bool
    {
        return ! is_null($this->previewCallback);
    }


    public function columnSelection(bool $active = true): static
    {
        $this->columnSelection = $active;

        return $this;
    }

    public function isColumnSelection(): bool
    {
        return $this->columnSelection;
    }

    public function nullable(Closure|bool|null $condition = null): static
    {
        $this->nullable = value($condition, $this) ?? true;

        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function horizontal(): static
    {
        $this->customWrapperAttributes([
            'class' => 'form-group-inline',
        ]);

        return $this;
    }

    protected function group(): static
    {
        $this->isGroup = true;

        return $this;
    }

    public function isGroup(): bool
    {
        return $this->isGroup;
    }

    public function withoutWrapper(mixed $condition = null): static
    {
        $this->withWrapper = value($condition, $this) ?? false;

        return $this;
    }

    public function hasWrapper(): bool
    {
        return $this->withWrapper;
    }

    public function insideLabel(): static
    {
        $this->isInsideLabel = true;

        return $this;
    }

    public function isInsideLabel(): bool
    {
        return $this->isInsideLabel;
    }

    public function beforeLabel(): static
    {
        $this->isBeforeLabel = true;

        return $this;
    }

    public function isBeforeLabel(): bool
    {
        return $this->isBeforeLabel;
    }

    public function onChangeMethod(
        string $method,
        array|Closure $params = [],
        ?string $message = null,
        ?string $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
    ): static {
        $url = static fn (?DataWrapperContract $data): ?string => $this->getCore()->getRouter()->getEndpoints()->method(
            method: $method,
            message: $message,
            params: array_filter([
                'resourceItem' => $data?->getKey(),
                ...value($params, $data?->getOriginal()),
            ], static fn ($value) => filled($value)),
            page: $page,
            resource: $resource,
        );

        return $this->onChangeUrl(
            url: $url,
            events: $events,
            selector: $selector,
            callback: $callback
        );
    }

    /**
     * @param  Closure(mixed $data, mixed $value, static $field): string  $url
     * @param  string[]  $events
     *
     * @return $this
     */
    public function onChangeUrl(
        Closure $url,
        HttpMethod $method = HttpMethod::GET,
        array $events = [],
        ?string $selector = null,
        ?AsyncCallback $callback = null,
    ): static {
        $this->onChangeUrl = $url;

        return $this->onChangeAttributes(
            method: $method,
            events: $events,
            selector: $selector,
            callback: $callback
        );
    }

    protected function onChangeAttributes(
        HttpMethod $method = HttpMethod::GET,
        array $events = [],
        ?string $selector = null,
        ?AsyncCallback $callback = null
    ): static {
        return $this->customAttributes(
            AlpineJs::asyncUrlDataAttributes(
                method: $method,
                events: $events,
                selector: $selector,
                callback: $callback,
            )
        );
    }

    protected function getOnChangeEventAttributes(?string $url = null): array
    {
        return $url ? AlpineJs::requestWithFieldValue($url, $this->getColumn()) : [];
    }

    protected function isOnChangeCondition(): bool
    {
        return true;
    }

    /**
     * @param  Closure(static $ctx): mixed  $callback
     */
    public function beforeRender(Closure $callback): static
    {
        $this->beforeRender = $callback;

        return $this;
    }

    public function getBeforeRender(): Renderable|string
    {
        return is_null($this->beforeRender)
            ? ''
            : value($this->beforeRender, $this);
    }

    /**
     * @param  Closure(static $ctx): mixed  $callback
     */
    public function afterRender(Closure $callback): static
    {
        $this->afterRender = $callback;

        return $this;
    }

    public function getAfterRender(): Renderable|string
    {
        return is_null($this->afterRender)
            ? ''
            : value($this->afterRender, $this);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function prepareBeforeRender(): void
    {
        if (! is_null($this->onChangeUrl) && $this->isOnChangeCondition()) {
            $onChangeUrl = value($this->onChangeUrl, $this->getData(), $this->toValue(), $this);

            $this->customAttributes(
                $this->getOnChangeEventAttributes($onChangeUrl),
            );
        }

        if (! $this->isPreviewMode()) {
            $id = $this->attributes->get('id');

            $this->customAttributes([
                $id ? 'id' : ':id' => $id ?? "\$id(`field-{$this->getFormName()}`)",
                'name' => $this->getNameAttribute(),
            ]);

            $this->resolveValidationErrorClasses();
        }
    }

    /**
     * @param  Closure(mixed $value, static $ctx): static  $callback
     */
    public function changeRender(Closure $callback): static
    {
        $this->renderCallback = $callback;

        return $this;
    }

    public function isRenderChanged(): bool
    {
        return ! is_null($this->renderCallback);
    }

    public function preview(): Renderable|string
    {
        if ($this->isRawMode()) {
            return (string) ($this->toRawValue() ?? '');
        }

        if ($this->isPreviewChanged()) {
            return (string) value(
                $this->previewCallback,
                $this->toValue(),
                $this,
            );
        }

        $preview = $this->resolvePreview();

        return $this->previewDecoration($preview);
    }

    protected function resolvePreview(): Renderable|string
    {
        return (string) ($this->toFormattedValue() ?? '');
    }

    private function previewDecoration(Renderable|string $value): Renderable|string
    {
        if ($value instanceof Renderable) {
            return $value->render();
        }

        if ($this->hasLink()) {
            $href = $this->getLinkValue($value);

            $value = (string) Url::make(
                href: $href,
                value: $this->getLinkName($value) ?: $value,
                icon: $this->getLinkIcon(),
                withoutIcon: $this->isWithoutIcon(),
                blank: $this->isLinkBlank()
            )->render();
        }

        if ($this->isBadge()) {
            return Badge::make((string) $value, $this->getBadgeColor($this->toValue()))
                ->render();
        }

        return $value;
    }

    protected function prepareRender(Renderable|Closure|string $view): Renderable|Closure|string
    {
        if (! $this->isPreviewMode() && $this->hasWrapper()) {
            return (new FieldContainer(
                field: $this,
                slot: $view,
            ))->render();
        }

        return $view;
    }

    protected function resolveRender(): Renderable|Closure|string
    {
        if (! $this->isDefaultMode() && $this->isRawMode()) {
            $this->previewMode();
        }

        if (! $this->isDefaultMode() && $this->isPreviewMode()) {
            return $this->preview();
        }

        if ($this->isRenderChanged()) {
            return value(
                $this->renderCallback,
                $this->toValue(),
                $this,
            );
        }

        if ($this->getView() === '') {
            return $this->toValue();
        }

        return $this->renderView();
    }

    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'isNullable' => $this->isNullable(),
        ];
    }
}
