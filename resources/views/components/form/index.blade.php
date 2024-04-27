@props([
    'buttons',
    'name' => null,
    'errors' => false,
    'precognitive' => false,
    'raw' => false
])
@if(formErrors($errors, $name)->isNotEmpty())
    <x-moonshine::form.all-errors :errors="formErrors($errors, $name)" />
@endif

<form
    {{ $attributes->merge(['class' => 'form', 'method' => 'POST']) }}
    @if(empty($attributes->get('id')))
        x-id="['form']" :id="$id('form')"
    @endif
>
    @if(strtolower($attributes->get('method', '')) !== 'get')
        @csrf
    @endif

        {{ $slot ?? '' }}

    @if(!$raw)
        <x-moonshine::layout.grid>
            <x-moonshine::layout.column>
                <div class="mt-3 flex w-full flex-wrap justify-start gap-2">
                    {{ $buttons ?? '' }}
                </div>
            </x-moonshine::layout.column>

            @if($precognitive)
                <x-moonshine::layout.column>
                    <div class="precognition_errors mb-6"></div>
                </x-moonshine::layout.column>
            @endif
        </x-moonshine::layout.grid>
    @endif
</form>
