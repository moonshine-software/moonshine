@if($hasErrorsAbove)
    <x-moonshine::form.all-errors :errors="$errors" />
@endif

<form
    {{ $formAttributes }}
    @if($hasAutoId)
        x-id="['form']" :id="$id('form')"
    @endif
>
    @if($hasCsrf)
        @csrf
    @endif

    @foreach($fields as $field)
        {!! $field !!}
    @endforeach

    @unless($raw)
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 xl:col-span-12 space-elements">
                <div class="mt-3 flex w-full flex-wrap justify-start gap-2">
                    {!! $submit !!}

                    @if($actionGroup)
                        {!! $actionGroup !!}
                    @endif
                </div>
            </div>

            @if($precognitive)
                <div class="col-span-12 xl:col-span-12 space-elements">
                    <div class="js-precognition-errors mb-6"></div>
                </div>
            @endif
        </div>
    @endunless
</form>
