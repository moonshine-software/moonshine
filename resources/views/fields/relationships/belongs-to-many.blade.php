<div x-id="['belongs-to-many']" :id="$id('belongs-to-many')">
    {{
        actionBtn(__('moonshine::ui.add'), to_page($element->getResource(), 'form-page', fragment: 'crud-form'))
            ->inModal(fn() => __('moonshine::ui.add'), fn() => '', async: true)
            ->showInLine()
            ->render()
    }}

    <x-moonshine::divider />

    {{ $element->value(withOld: false)->render() }}
</div>
