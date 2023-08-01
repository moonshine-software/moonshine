@props([
    'route',
    'title' => '',
    'filled' => false
])

<div x-data="{ id: $id('async-modal') }">
    <x-moonshine::modal wide :title="$title">
        <div :id="id">
            <x-moonshine::loader />
        </div>

        <x-slot name="outerHtml">
            <div x-data="asyncData">
                <x-moonshine::link
                        :filled="$filled"
                        :attributes="$attributes"
                        @click.prevent="toggleModal; load('{!! str_replace('&amp;', '&', $route) !!}', id);"
                >
                    {{ $slot ?? '' }}
                </x-moonshine::link>
            </div>
        </x-slot>
    </x-moonshine::modal>
</div>
