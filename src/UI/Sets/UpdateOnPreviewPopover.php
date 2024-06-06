<?php

declare(strict_types=1);

namespace MoonShine\UI\Sets;

use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Popover;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Text;

final class UpdateOnPreviewPopover
{
    public function __construct(private Field $field, private string $component)
    {
    }

    public function __invoke(): Popover
    {
        $name = 'update-on-preview-' . spl_object_id($this->field);

        return Popover::make(
            '',
            (string) Link::make(
                '#',
                $this->field->toFormattedValue()
            )->icon('pencil')
        )
            ->name($name)
            ->showOnClick()
            ->content(fn () => (string) FormBuilder::make()
                ->method(FormMethod::POST)
                ->action(
                    moonshineRouter()->getEndpoints()->updateColumn(
                        extra: [
                            'resourceItem' => $this->field->getData()->getKey(),
                        ]
                    )
                )
                ->async(events: [
                    AlpineJs::event(JsEvent::POPOVER_TOGGLED, $name),
                    AlpineJs::event(JsEvent::TABLE_ROW_UPDATED, $this->component . "-" . $this->field->getData()->getKey()),
                ])
                ->fields([
                    Flex::make([
                        Hidden::make('_method')->setValue('PUT'),
                        Hidden::make('field')->setValue('title'),
                        Text::make('Title', 'value')
                            ->style('margin: 0!important')
                            ->setValue($this->field->toFormattedValue())
                            ->withoutWrapper(),
                    ]),
                ])
                ->submit('OK', ['class' => 'btn-primary'])
            );
    }
}
