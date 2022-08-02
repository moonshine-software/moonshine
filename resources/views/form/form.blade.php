<div class="w-full">
    @include('moonshine::form.shared.errors', ['errors' => $errors])

    <form {{ $form->attributes()->merge(['class' => 'bg-white dark:bg-darkblue shadow-md rounded-lg mb-4 text-white']) }}>
        @csrf

        @foreach($form as $element)
            @if($element instanceof \Leeto\MoonShine\Decorations\Decoration)

            @elseif($element instanceof \Leeto\MoonShine\Fields\Field && $element->showOnForm)
                <x-moonshine::field-container :field="$element">
                    {!! $element !!}
                </x-moonshine::field-container>
            @endif
        @endforeach


        <div class="px-10 py-10">
            @include('moonshine::form.shared.btn', [
                'type' => 'submit',
                'class' => '',
                'name' => trans('moonshine::ui.save')
            ])
        </div>
    </form>
</div>
