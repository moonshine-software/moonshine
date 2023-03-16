@props([
    'button',
    'buttons',
    'errors' => false
])

@if($errors)
    <x-moonshine::form.all-errors :errors="$errors" />
@endif

<form
    {{ $attributes->merge(['class' => 'form', 'method' => 'POST']) }}
>
    @csrf

    {{ $slot }}

    <div class="col-span-12 xl:col-span-12">
        <div class="mt-3 flex w-full flex-wrap justify-start gap-2">
            {{ $buttons ?? '' }}

            @if($button ?? false)
                <x-moonshine::form.button :attributes="$button->attributes->class(['btn btn-primary btn-lg'])->merge(['type' => 'submit'])">
                    {{ $button }}
                </x-moonshine::form.button>
            @endif
        </div>
    </div>

    <div class="precognition_errors"></div>
</form>
