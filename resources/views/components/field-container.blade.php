@props([
    'resource',
    'field',
    'item'
])
@if($field->isHidden())
    <div class="hidden">{{ $slot }}</div>
@else
    <x-moonshine::form.input-wrapper
        :attributes="$field->attributes()->merge([
            'x-show' => $field->showWhenState ? 'whenFields.'.$field->showWhenField . '==`'.$field->showWhenValue.'`' : 'true'
        ])"
        label="{{ $field->label() }}"
        name="{{ $field->name() }}"
        :expansion="$field->ext()"
    >
    
        @if($field->hasLink())
            <x-slot:beforeSlot>
                <x-moonshine::link
                    class="my-2"
                    :href="$field->getLinkValue()"
                    :_target="$field->isLinkBlank() ? 'blank' : 'self'"
                >
                    {{ $field->getLinkName() }}
                </x-moonshine::link>
            </x-slot:beforeSlot>
        @endif

        {{ $slot }}
        
        @if($field->getHint())
            <x-slot:afterSlot>
                <x-moonshine::form.hint>
                    {{ $field->getHint() }}
                </x-moonshine::form.hint>
            </x-slot:afterSlot>
        @endif
    </x-moonshine::form.input-wrapper>
@endif
