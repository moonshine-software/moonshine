<div class="my-5">
    <x-moonshine::link
        :href="$element->getLinkValue()"
        :target="$element->isLinkBlank() ? '_blank' : '_self'"
        :filled="true"
        :icon="$element->iconValue()"
        :attributes="$element->attributes()"
    >
        {{ $element->getLinkName() }}
    </x-moonshine::link>
</div>
