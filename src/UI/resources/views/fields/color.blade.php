@props([
    'value' => '',
])
<div x-data="{ color: '{!! $value ?? "#000000" !!}' }">
    <div class="flex items-center">
        <div class="relative mr-[-2.3rem] ml-[0.3rem] w-8 h-8 rounded-md overflow-hidden">
            <x-moonshine::form.input
                :attributes="$attributes->except('type')->merge([
                    'type' => 'color',
                ])"
                class="absolute -top-2 -left-2 w-16 h-16 rounded-full"
                x-model:value="color"
            />
        </div>

        <x-moonshine::form.input
            :attributes="$attributes->except(['type'])->merge([
                'type' => 'text',
                'placeholder' => '#000000',
            ])"
            style="padding-left: 45px;"
            x-model:value="color"
        />
    </div>
</div>
