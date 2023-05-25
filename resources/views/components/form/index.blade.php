@props([
    'button',
    'buttons',
    'errors' => false,
    'raw' => false
])

@if($errors)
    <x-moonshine::form.all-errors :errors="$errors" />
@endif

<form
    {{ $attributes->merge(['class' => 'form', 'method' => 'POST']) }}
>
    @if(strtolower($attributes->get('method', '')) !== 'get')
        @csrf
    @endif

    {{ $slot }}

    @if(!$raw)
        <x-moonshine::grid>
            <x-moonshine::column>
                <div class="mt-3 flex w-full flex-wrap justify-start gap-2">
                    @if($button ?? false)
                        <x-moonshine::form.button :attributes="$button->attributes->merge(['type' => 'submit'])">
                            {{ $button }}
                        </x-moonshine::form.button>
                    @endif

                    {{ $buttons ?? '' }}
                </div>
            </x-moonshine::column>

            <x-moonshine::column>
                <div class="precognition_errors mb-6"></div>
            </x-moonshine::column>
        </x-moonshine::grid>
    @endif
</form>
