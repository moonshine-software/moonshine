@props([
    'buttons',
    'name' => null,
    'errors' => false,
    'precognitive' => false,
    'raw' => false,
    'isShowErrorsAtFormTop' => true,
])

@if($isShowErrorsAtFormTop && formErrors($errors, $name)->isNotEmpty())
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

    {{ $slot }}

    @if(!$raw)
        <x-moonshine::grid>
            <x-moonshine::column>
                <div class="mt-3 flex w-full flex-wrap justify-start gap-2">
                    {{ $buttons ?? '' }}
                </div>
            </x-moonshine::column>

            @if($precognitive)
                <x-moonshine::column>
                    <div class="precognition_errors mb-6"></div>
                </x-moonshine::column>
            @endif
        </x-moonshine::grid>
    @endif
</form>
