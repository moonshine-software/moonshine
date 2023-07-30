<div class="sm:flex
    @if(!$element->isWithoutSpace()) gap-4 @endif
    items-{{ $element->getItemsAlign() }}
    justify-{{ $element->getJustifyAlign() }}"
>
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="true"
    />
</div>
