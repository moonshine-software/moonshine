<div x-id="['json']" :id="$id('json')" {{ $element->attributes()->except('x-on:change') }}>
    {{ $element->value(withOld: false)
            ->editable()
            ->when(
                !$element->isAsRelation(),
                fn($table) => $table->sortable()
            )
            ->when(
                $element->isCreatable(),
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
