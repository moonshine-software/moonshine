<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\HiddenIds;
use MoonShine\Support\AlpineJs;
use MoonShine\UI\Modal;

trait WithModal
{
    protected ?Modal $modal = null;

    public function isInModal(): bool
    {
        return ! is_null($this->modal);
    }

    public static function makeModal(
        Closure|string $button,
        Closure|string $title,
        string $url,
        ?Closure $component
    ): ActionButton {
        if (! is_closure($title)) {
            $title = static fn (): Closure|string => $title;
        }

        return ActionButton::make($button, $url)
            ->inModal($title, $component);
    }

    public function inModal(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        array $buttons = [],
        bool $async = false,
        bool $wide = false,
        bool $auto = false,
        bool $closeOutside = false,
        array $attributes = [],
        bool $autoClose = true,
    ): static {
        $this->modal = Modal::make($title, $content, $async)
            ->auto($auto)
            ->wide($wide)
            ->autoClose($autoClose)
            ->closeOutside($closeOutside)
            ->buttons($buttons);

        if ($attributes !== []) {
            $this->modal->customAttributes($attributes);
        }

        return $this;
    }

    public function withConfirm(
        Closure|string|null $title = null,
        Closure|string|null $content = null,
        Closure|string|null $button = null,
        Closure|array|null $fields = null,
        string $method = 'POST',
        bool $async = false,
        ?Closure $formBuilder = null
    ): static {
        $isDefaultMethods = in_array(strtolower($method), ['get', 'post']);

        $this->modal = Modal::make(
            title: is_null($title) ? __('moonshine::ui.confirm') : $title,
            content: fn (mixed $data): string => (string) FormBuilder::make(
                $this->url($data),
                $isDefaultMethods ? $method : 'POST'
            )->fields(
                array_filter([
                    $isDefaultMethods
                        ? null
                        : Hidden::make('_method')->setValue($method),

                    $this->isBulk()
                        ? HiddenIds::make($this->bulkForComponent())
                        : null,

                    ...(is_null($fields) ? [] : value($fields, $data)),

                    Heading::make(
                        is_null($content)
                            ? __('moonshine::ui.confirm_message')
                            : value($content, $data)
                    ),
                ])
            )->when(
                $async && ! $this->isAsyncMethod(),
                fn (FormBuilder $form): FormBuilder => $form->async()
            )
                ->when(
                    ! is_null($formBuilder),
                    fn (FormBuilder $form): FormBuilder => value($formBuilder, $form, $data)
                )
                ->when(
                    $this->isAsyncMethod(),
                    fn (FormBuilder $form): FormBuilder => $form->asyncMethod($this->asyncMethod())
                )
                ->submit(
                    is_null($button)
                        ? __('moonshine::ui.confirm')
                        : value($button, $data),
                    ['class' => 'btn-secondary']
                )
        )->auto();

        if ($this->isBulk()) {
            $this->attributes()->setAttributes([
                'data-button-type' => 'modal-button',
            ]);
        }

        // In this case, the form inside the modal works in async mode,
        // so the async mode is removed from the button.
        if ($this->isAsyncMethod()) {
            $this->purgeAsync();
        }

        return $this;
    }

    public function modal(): ?Modal
    {
        return $this->modal;
    }

    public function toggleModal(string $name = 'default'): static
    {
        return $this->onClick(fn (): string => "\$dispatch('" . AlpineJs::event(JsEvent::MODAL_TOGGLED, $name) . "')");
    }

    public function openModal(): static
    {
        return $this->onClick(fn (): string => 'toggleModal');
    }
}
