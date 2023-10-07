<div x-id="['json']" :id="$id('json')" {{ $element->attributes()->only('class') }} data-field-block="{{ $element->column() }}">
    {{ $element->value(withOld: false)
            ->editable()
            ->reindex()
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
