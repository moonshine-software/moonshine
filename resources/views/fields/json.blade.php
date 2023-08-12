<div x-id="['json']" :id="$id('json')">
    {{ $element->value()
            ->editable()
            ->buttons([
                actionBtn('', '#')
                    ->icon('heroicons.outline.trash')
                    ->onClick(fn($action) => '$el.closest("tr").remove()', 'prevent')
                    ->customAttributes(['class' => 'btn-pink'])
                    ->showInLine(),
            ])
            ->preview()
            ->render()
    }}

    <x-moonshine::divider />

    <x-moonshine::link
        class="w-full"
        icon="heroicons.plus-circle"
    >
        @lang('moonshine::ui.add')
    </x-moonshine::link>
</div>
