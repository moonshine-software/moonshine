<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\ActionButton;

use Closure;
use Illuminate\Support\Str;
use JsonException;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Heading;
use MoonShine\UI\Components\Modal;
use MoonShine\UI\Exceptions\ActionButtonException;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\HiddenIds;

trait WithModal
{
    /**
     * @var null|Closure(mixed, ?DataWrapperContract, static): ModalContract
     */
    protected ?Closure $modal = null;

    /** @phpstan-assert-if-true Closure $this->modal */
    public function isInModal(): bool
    {
        return ! \is_null($this->modal);
    }

    /**
     * @param  ?Closure(ModalContract $modal, ActionButtonContract $ctx): ModalContract  $builder
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $title
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $content
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $name
     */
    public function inModal(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $name = null,
        ?Closure $builder = null,
        iterable $components = []
    ): static {
        $name ??= static fn (mixed $data, ActionButtonContract $ctx): string => Str::random(6) . $ctx->getData()?->getKey();

        $async = $this->purgeAsyncTap();

        $this->modal = static fn (mixed $item, ?DataWrapperContract $data, ActionButtonContract $ctx) => Modal::make(
            title: static fn () => ($title instanceof Closure ? $title($item, $ctx) : $title) ?? $ctx->getLabel(),
            content: static fn () => ($content instanceof Closure ? $content($item, $ctx) : $content) ?? '',
            asyncUrl: $async ? static fn (): string => $ctx->getUrl($item) : null,
            components: $components
        )
            ->name(($name instanceof Closure ? $name($item, $ctx) : $name))
            ->when(
                ! \is_null($builder),
                static fn (ModalContract $modal): ModalContract => $builder === null ? $modal : $builder($modal, $ctx)
            );

        return $this->onBeforeRender(
            static fn (ActionButtonContract $ctx): ActionButtonContract => $ctx->toggleModal(
                $ctx->getComponent()?->getName() ?? ($name instanceof Closure ? $name($ctx->getData()?->getOriginal(), $ctx) : $name)
            )
        );
    }

    /**
     * @param  ?Closure(FormBuilderContract $form, mixed $data): FormBuilderContract  $formBuilder
     * @param  ?Closure(ModalContract $modal, ActionButtonContract $ctx): ModalContract  $modalBuilder
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $title
     * @param (Closure(mixed): string)|string|null $content
     * @param (Closure(mixed): string)|string|null $button
     * @param (Closure(mixed): list<ComponentContract>)|list<ComponentContract>|null $fields
     * @param (Closure(mixed, ActionButtonContract): string)|string|null $name
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
            static fn (mixed $item, ActionButtonContract $ctx) => ($title instanceof Closure ? $title($item, $ctx) : $title) ?? $ctx->getCore()->getTranslator()->getString('moonshine::ui.confirm'),
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
                            ? $ctx->getCore()->getTranslator()->getString('moonshine::ui.confirm_message')
                            : value($content, $item)
                    ),
                ])
            )->when(
                ! \is_null($selector),
                static fn (FormBuilderContract $form): FormBuilderContract => $form->asyncSelector($selector ?? '')
            )->when(
                $async,
                static fn (FormBuilderContract $form): FormBuilderContract => $form->async(events: $events, callback: $callback)
            )->submit(
                button: ActionButton::make(
                    \is_null($button)
                        ? $ctx->getCore()->getTranslator()->getString('moonshine::ui.confirm')
                        : value($button, $item)
                )->error()
            )->when(
                ! \is_null($formBuilder),
                static fn (FormBuilderContract $form): FormBuilderContract => $formBuilder === null ? $form : $formBuilder($form, $item)
            )->when(
                $ctx->getAttribute('data-async-response-type') !== null,
                static fn (FormBuilderContract $form): FormBuilderContract => $form->download()
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

    /**
     * @throws JsonException
     */
    public function toggleModal(Closure|string $name = 'default', Closure|string|null $asyncUrl = null): static
    {
        return $this->onClick(
            static function (ActionButtonContract $ctx) use ($name, $asyncUrl): string {
                $original = $ctx->getData()?->getOriginal();
                $resolvedName = Str::lower((string) (($name instanceof Closure ? $name($original, $ctx) : $name) ?? 'default'));
                $resolvedAsyncUrl = ($asyncUrl instanceof Closure ? $asyncUrl($original, $ctx) : $asyncUrl);

                if (! \is_null($resolvedAsyncUrl) && ! \is_string($resolvedAsyncUrl)) {
                    throw new ActionButtonException(
                        \sprintf(
                            'asyncUrl must be string|null, %s given',
                            get_debug_type($resolvedAsyncUrl)
                        )
                    );
                }

                $params = json_encode(
                    $resolvedName,
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ) ?: '"default"';

                if (\is_string($resolvedAsyncUrl)) {
                    $asyncUrlJson = json_encode(
                        $resolvedAsyncUrl,
                        JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    ) ?: 'null';
                    $params .= ", $asyncUrlJson";
                }

                return "window.MoonShine.ui.toggleModal($params)";

            },
            'prevent'
        );
    }

    public function openModal(): static
    {
        return $this->onClick(static fn (): string => 'toggleModal', 'prevent');
    }
}
