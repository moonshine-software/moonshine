<?php

declare(strict_types=1);

namespace MoonShine\UI\Sets;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\EventParams\ListRowEventParams;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Components\Popover;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Fields\Hidden;

final readonly class UpdateOnPreviewPopover
{
    /**
     * @param  FieldContract&HasUpdateOnPreviewContract  $field
     */
    public function __construct(
        private HasUpdateOnPreviewContract $field,
        private string $label,
        private string $component,
        private string $route,
    ) {
    }

    public function __invoke(): Popover
    {
        $name = 'update-on-preview-' . spl_object_id($this->field);

        return Popover::make(
            '',
            (string)Link::make(
                'javascript:void(0);',
                (string)$this->field->toFormattedValue(),
            )->icon('pencil'),
        )
            ->name($name)
            ->showOnClick()
            ->content(
                fn (): string
                    => (string)FormBuilder::make()
                    ->method(FormMethod::POST)
                    ->action($this->route)
                    ->async(events: [
                        AlpineJs::event(JsEvent::POPOVER_TOGGLED, $name),
                        AlpineJs::event(
                            event: JsEvent::TABLE_ROW_UPDATED,
                            name: $this->component,
                            params: ListRowEventParams::make($this->field->getData()?->getKey() ?? $this->field->getRowIndex())
                        ),
                    ])
                    ->fields([
                        Flex::make([
                            Hidden::make('_method')->setValue('PUT'),
                            Hidden::make('field')->setValue($this->field->getColumn()),
                            $this->field
                                ->style('margin: 0!important')
                                ->setColumn('value')
                                ->customAttributes([
                                    'name' => 'value',
                                ])
                                ->withoutWrapper()
                                ->disableUpdateOnPreview(),
                        ]),
                    ])
                    ->submit($this->label, ['class' => 'btn-primary']),
            );
    }
}
