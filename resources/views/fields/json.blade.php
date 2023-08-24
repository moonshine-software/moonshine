<div x-id="['json']" :id="$id('json')">
    {{ $element->value(withOld: false)
            ->editable()
            ->buttons([
                actionBtn('', '#')
                    ->icon('heroicons.outline.trash')
                    ->onClick(fn($action) => 'remove()', 'prevent')
                    ->customAttributes(['class' => 'btn-pink'])
                    ->showInLine(),
            ])
            ->simple()
            ->render()
    }}
</div>
