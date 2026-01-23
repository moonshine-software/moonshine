@props([
    'color' => null,
    'translates' => [],
])
<div {{ $attributes->merge(['class' => 'snippet'.($color ? ' snippet-'.$color : '')]) }}
     x-data="{
        copy() {
            navigator.clipboard.writeText($refs.content.textContent);
        }
     }"
>
    <code class="snippet-content" x-ref="content">{{ $slot }}</code>
    <button @click.prevent="copy()"
            class="snippet-copy"
            type="button"
            x-data="tooltip('{{ $translates['copied'] ?? '' }}', {placement: 'top', trigger: 'click', delay: [0, 800]})"
    >
        <x-moonshine::icon icon="document-duplicate" size="4" />
    </button>
</div>
