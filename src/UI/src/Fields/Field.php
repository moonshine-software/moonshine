<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\DTOs\AsyncCallback;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\TextWrap;
use MoonShine\UI\Components\Badge;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\WithBadge;
use MoonShine\UI\Traits\Fields\WithHint;
use MoonShine\UI\Traits\Fields\WithLink;
use MoonShine\UI\Traits\Fields\WithSorts;

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
    use Reactivity;

    protected bool $defaultMode = false;

    protected bool $previewMode = false;

    protected bool $rawMode = false;

    protected ?Closure $previewCallback = null;

    protected ?Closure $renderCallback = null;

    protected ?Closure $beforeRender = null;

    protected ?Closure $afterRender = null;

    protected bool $withWrapper = true;

    protected bool $columnSelection = true;

    protected bool $columnHideOnInit = false;

    protected bool $stickyColumn = false;

    protected bool $nullable = false;

    protected bool $isBeforeLabel = false;

    protected bool $isInsideLabel = false;

    protected ?Closure $onChangeUrl = null;

    protected ?TextWrap $textWrap = null;

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

    public function rawMode(): static
    {
        $this->rawMode = true;

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
        return ! \is_null($this->previewCallback);
    }


    public function columnSelection(bool $active = true, bool $hideOnInit = false): static
    {
        $this->columnSelection = $active;
        $this->columnHideOnInit = $hideOnInit;

        return $this;
    }

    public function isColumnSelection(): bool
    {
        return $this->columnSelection;
    }

    public function isColumnHideOnInit(): bool
    {
        return $this->columnHideOnInit;
    }

    public function sticky(): static
    {
        $this->stickyColumn = true;

        return $this;
    }

    public function isStickyColumn(): bool
    {
        return $this->stickyColumn;
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
        $this->wrapperClass('form-group-inline');

        return $this;
    }

    public function withoutWrapper(Closure|bool|null $condition = null): static
    {
        $result = value($condition, $this) ?? true;

        $this->withWrapper = ! $result;

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
        null|string|array $selector = null,
        array $events = [],
        ?AsyncCallback $callback = null,
        ?PageContract $page = null,
        ?ResourceContract $resource = null,
    ): static {
        $url = fn (?DataWrapperContract $data): string => $this->getCore()->getRouter()->getEndpoints()->method(
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
     */
    public function onChangeUrl(
        Closure $url,
        HttpMethod $method = HttpMethod::PUT,
        array $events = [],
        null|string|array $selector = null,
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

    public function onChangeEvent(array|string $events, array $exclude = [], bool $withoutPayload = false): static
    {
        $excludes = $withoutPayload ? '*' : implode(',', [
            ...$exclude,
            '_component_name',
            '_token',
            '_method',
        ]);

        return $this->customAttributes([
            '@change' => "dispatchEvents(
                 `" . AlpineJs::prepareEvents($events) . "`,
                 `$excludes`
             )",
        ]);
    }

    /**
     * @param  string[]  $events
     * @param  string|string[]|null  $selector
     */
    protected function onChangeAttributes(
        HttpMethod $method = HttpMethod::GET,
        array $events = [],
        null|string|array $selector = null,
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

    /**
     * @return array<string, string>
     */
    protected function getOnChangeEventAttributes(?string $url = null): array
    {
        return $url ? AlpineJs::onChangeSaveField($url, $this->getColumn()) : [];
    }

    protected function isOnChangeCondition(): bool
    {
        return true;
    }

    protected function resolveAssets(): void
    {
        if (! $this->isConsoleMode() && ! $this->isPreviewMode()) {
            $this->getAssetManager()->add($this->getAssets());
        }
    }

    protected function shouldUseAssets(): bool
    {
        return ! $this->isPreviewMode();
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
        return \is_null($this->beforeRender)
            ? ''
            : \call_user_func($this->beforeRender, $this);
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
        return \is_null($this->afterRender)
            ? ''
            : \call_user_func($this->afterRender, $this);
    }

    protected function prepareBeforeRender(): void
    {
        if (! \is_null($this->onChangeUrl) && $this->isOnChangeCondition()) {
            $onChangeUrl = \call_user_func($this->onChangeUrl, $this->getData(), $this->toValue(), $this);

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
     * @param  Closure(mixed $value, static $ctx): string  $callback
     */
    public function changeRender(Closure $callback): static
    {
        $this->renderCallback = $callback;

        return $this;
    }

    public function isRenderChanged(): bool
    {
        return ! \is_null($this->renderCallback);
    }

    public function textWrap(TextWrap $wrap): static
    {
        $this->textWrap = $wrap;

        return $this;
    }

    public function getTextWrap(): ?TextWrap
    {
        return $this->textWrap;
    }

    public function hasTextWrap(): bool
    {
        return $this->textWrap instanceof TextWrap;
    }

    public function withoutTextWrap(): static
    {
        $this->textWrap = null;

        return $this;
    }

    public function preview(): Renderable|string
    {
        if ($this->isRawMode()) {
            return (string) ($this->toRawValue() ?? '');
        }

        if ($this->isPreviewChanged()) {
            return (string) \call_user_func(
                $this->previewCallback,
                $this->toValue(),
                $this,
            );
        }

        $preview = $this->resolvePreview();
        $decorated = $this->previewDecoration($preview);

        if ($this->hasTextWrap()) {
            return Str::wrap(
                (string) $decorated,
                '<div class="text-' . $this->getTextWrap()->value . '">',
                '</div>'
            );
        }

        return $decorated;
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

            $value = (string) Link::make(
                href: $href,
                label: $this->getLinkName($value) ?: $value,
            )
                ->when(
                    ! $this->isWithoutIcon() && $this->getLinkIcon() !== null,
                    fn (Link $ctx): Link => $ctx->icon($this->getLinkIcon())
                )
                ->when(
                    $this->isLinkBlank(),
                    fn (Link $ctx): Link => $ctx->blank()
                )
                ->render();
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

    protected function resolveSelector(): void
    {
        if ($this->hasWrapper()) {
            $this->customWrapperAttributes([
                'data-field-selector' => $this->getNameDot(),
            ]);
        } else {
            $this->customAttributes([
                'data-field-selector' => $this->getNameDot(),
            ]);
        }
    }

    protected function resolveRender(): Renderable|Closure|string
    {
        $this->resolveSelector();

        if (! $this->isDefaultMode() && $this->isRawMode()) {
            $this->previewMode();
        }

        if (! $this->isDefaultMode() && $this->isPreviewMode()) {
            return $this->preview();
        }

        if ($this->isRenderChanged()) {
            $render = \call_user_func(
                $this->renderCallback,
                $this->toValue(),
                $this,
            );

            return $render instanceof FieldContract ? $render->render() : $render;
        }

        if ($this->getView() === '') {
            return $this->toValue();
        }

        return $this->renderView();
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            ...parent::systemViewData(),
            'isNullable' => $this->isNullable(),
        ];
    }
}
