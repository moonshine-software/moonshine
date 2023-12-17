<div x-id="['json']"
     :id="$id('json')"
     {{ $element->attributes()->only('class') }}
     data-field-block="{{ $element->column() }}"
>
    {{ $element->value(withOld: false)
            ->editable()
            ->reindex()
            ->when(
                !$element->isAsRelation(),
                fn($table) => $table->sortable()
            )
            ->when(
                $element->isCreatable(),
                fn($table) => $table->creatable(
                    limit: $element->creatableLimit(),
                    button: $element->creatableButton()
                )
            )
            ->buttons($element->getButtons())
            ->simple()
            ->render()
    }}
</div>
