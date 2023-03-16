<div class="my-5">
    <x-moonshine::link
        :href="$decoration->getLinkValue()"
        :_target="$decoration->isLinkBlank() ? '_blank' : '_self'"
        :filled="true"
        :icon="$decoration->iconValue()"
    >
        {{ $decoration->getLinkName() }}
    </x-moonshine::link>
</div>
