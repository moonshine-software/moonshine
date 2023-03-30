<div class="my-5">
    <x-moonshine::link
        :href="$element->getLinkValue()"
        :_target="$element->isLinkBlank() ? '_blank' : '_self'"
        :filled="true"
        :icon="$element->iconValue()"
    >
        {{ $element->getLinkName() }}
    </x-moonshine::link>
</div>
