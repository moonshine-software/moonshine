<div x-id="['json']" :id="$id('json')">
    {{ $element->value(withOld: false)
            ->editable()
            ->when(
                $element->isCreatable() || is_iterable($element->toValue()),
                fn($table) => $table->creatable()
            )
            ->when(
                $element->isRemovable(),
                fn($table) => $table->buttons([
                    actionBtn('', '#')
                        ->icon('heroicons.outline.trash')
                        ->onClick(fn($action) => 'remove()', 'prevent')
                        ->customAttributes(['class' => 'btn-secondary'])
                        ->showInLine(),
                ])
            )
            ->simple()
            ->render()
    }}
</div>
