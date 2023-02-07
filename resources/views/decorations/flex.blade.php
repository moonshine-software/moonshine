<div class="md:flex
    @if(!$decoration->isWithoutSpace()) gap-4 @endif
    items-{{ $decoration->getItemsAlign() }}
    justify-{{ $decoration->getJustifyAlign() }}"
>
    <x-moonshine::resource-renderable
        :components="$decoration->fields()"
        :item="$item"
        :resource="$resource"
        :container="true"
    />
</div>
