<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Hidden;
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
    ): static {
        $this->modal = Modal::make($title, $content, $async)
            ->auto($auto)
            ->wide($wide)
            ->closeOutside($closeOutside)
            ->buttons($buttons)
        ;

        if(! empty($attributes)) {
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

                    ...(is_null($fields) ? [] : value($fields, $data)),

                    Heading::make(
                        is_null($content)
                            ? __('moonshine::ui.confirm_message')
                            : value($content, $data)
                    ),
                ])
            )->when(
                $async,
                fn (FormBuilder $form): FormBuilder => $form->async()
            )
            ->when(
                ! is_null($formBuilder),
                fn (FormBuilder $form): FormBuilder => value($formBuilder, $form, $data)
            )->submit(
                is_null($button)
                    ? __('moonshine::ui.confirm')
                    : value($button, $data),
                ['class' => 'btn-secondary']
            )
        )->auto();

        return $this;
    }

    public function modal(): ?Modal
    {
        return $this->modal;
    }
}
