<div class="sm:flex
    @if(!$element->isWithoutSpace()) gap-4 @endif
    items-{{ $element->getItemsAlign() }}
    justify-{{ $element->getJustifyAlign() }}"
>
    <x-moonshine::resource-renderable
        :components="$element->getFields()"
        :item="$item"
        :resource="$resource"
        :container="true"
    />
</div>
