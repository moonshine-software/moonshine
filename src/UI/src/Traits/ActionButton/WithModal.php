<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\ActionButton;

use Closure;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\HiddenIds;

trait WithModal
{
    /**
     * @var null|Closure(mixed, DataWrapperContract, static): ModalContract
     */
    protected ?Closure $modal = null;

    public function isInModal(): bool
    {
        return ! \is_null($this->modal);
    }

    /**
     * @param  ?Closure(ModalContract $modal, ActionButtonContract $ctx): ModalContract  $builder
     */
    public function inModal(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $name = null,
        ?Closure $builder = null,
        iterable $components = [],
    ): static {
        if (\is_null($name)) {
            $name = fn (mixed $data, ActionButtonContract $ctx): string => spl_object_id($this) . $ctx->getData()?->getKey();
        }

        $async = $this->purgeAsyncTap();

        $this->modal = static fn (mixed $item, ?DataWrapperContract $data, ActionButtonContract $ctx) => Modal::make(
            title: static fn () => value($title, $item, $ctx) ?? $ctx->getLabel(),
            content: static fn () => value($content, $item, $ctx) ?? '',
            asyncUrl: $async ? static fn (): string => $ctx->getUrl($item) : null,
            components: $components
        )
            ->name(value($name, $item, $ctx))
            ->when(
                ! \is_null($builder),
                static fn (ModalContract $modal): ModalContract => $builder($modal, $ctx)
            );

        return $this->onBeforeRender(
            static fn (ActionButtonContract $ctx): ActionButtonContract => $ctx->toggleModal(
                value($name, $ctx->getData()?->getOriginal(), $ctx)
            )
        );
    }

    /**
     * @param  ?Closure(FormBuilderContract $form, mixed $data): FormBuilderContract  $formBuilder
     * @param  ?Closure(ModalContract $modal, ActionButtonContract $ctx): ModalContract  $modalBuilder
     */
    public function withConfirm(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $button = null,
        Closure|array|null $fields = null,
        HttpMethod $method = HttpMethod::POST,
        /** @var null|Closure(FormBuilderContract, mixed): FormBuilderContract $formBuilder */
        ?Closure $formBuilder = null,
        ?Closure $modalBuilder = null,
        Closure|string|null $name = null,
    ): static {
        $method = $this->asyncHttpMethod ?: $method;
        $events = $this->asyncEvents;
        $callback = $this->asyncCallback;
        $selector = $this->asyncSelector;

        $isDefaultMethods = \in_array($method, [HttpMethod::GET, HttpMethod::POST], true);
        $async = $this->purgeAsyncTap();

        if ($this->isBulk()) {
            $this->customAttributes([
                'data-button-type' => 'modal-button',
            ]);
        }

        return $this->inModal(
            static fn (mixed $item, ActionButtonContract $ctx) => value($title, $item, $ctx) ?? $ctx->getCore()->getTranslator()->get('moonshine::ui.confirm'),
            static fn (mixed $item, ActionButtonContract $ctx): string => (string) FormBuilder::make(
                $ctx->getUrl($item),
                $isDefaultMethods ? FormMethod::from($method->value) : FormMethod::POST
            )->fields(
                array_filter([
                    $isDefaultMethods
                        ? null
                        : Hidden::make('_method')->setValue($method->value),

                    $ctx->isBulk()
                        ? HiddenIds::make($ctx->getBulkForComponent())
                        : null,

                    ...(\is_null($fields) ? [] : value($fields, $item)),

                    Heading::make(
                        \is_null($content)
                            ? $ctx->getCore()->getTranslator()->get('moonshine::ui.confirm_message')
                            : value($content, $item)
                    ),
                ])
            )->when(
                ! \is_null($selector),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->asyncSelector($selector)
            )->when(
                $async,
                static fn (FormBuilderContract $form): FormBuilderContract => $form->async(events: $events, callback: $callback)
            )->submit(
                button: ActionButton::make(
                    \is_null($button)
                        ? $ctx->getCore()->getTranslator()->get('moonshine::ui.confirm')
                        : value($button, $item)
                )->error()
            )->when(
                ! \is_null($formBuilder),
                static fn (FormBuilderContract $form): FormBuilderContract => $formBuilder($form, $item)
            )->when(
                $ctx->getAttribute('data-async-response-type') !== null,
                static fn (FormBuilder $form): FormBuilder => $form->download()
            ),
            name: $name,
            builder: $modalBuilder
        );
    }

    public function getModal(): ?ComponentContract
    {
        if (! $this->isInModal()) {
            return null;
        }

        return \call_user_func($this->modal, $this->getData()?->getOriginal(), $this->getData(), $this);
    }

    public function toggleModal(string $name = 'default'): static
    {
        return $this->onClick(
            static fn (mixed $data): string => "\$dispatch('" . AlpineJs::event(JsEvent::MODAL_TOGGLED, $name) . "')",
            'prevent'
        );
    }

    public function openModal(): static
    {
        return $this->onClick(static fn (): string => 'toggleModal', 'prevent');
    }
}
