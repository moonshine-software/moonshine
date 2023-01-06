<div class="flex
    @if(!$decoration->isWithoutSpace()) space-x-4 @endif
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
