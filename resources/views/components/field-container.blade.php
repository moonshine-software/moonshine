@if($field->isHidden())
    <div class="hidden">{{ $slot }}</div>
@else
<div {!! $field->showWhenState ? "x-show='".$field->showWhenField." == `".$field->showWhenValue ."`'" : ''!!}
     class="border-b border-whiteblue dark:border-dark px-10 py-5"
>
    <div>
        <div class="px-4 py-5 sm:grid sm:gap-2 {{ $field->isFullWidth()  ? 'grid-cols-1' : 'sm:grid-cols-4' }} sm:px-2">
            <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                @include("moonshine::fields.shared.label", ["field" => $field])

                @if($field->hasLink())
                    <a class="block mt-5 text-purple underline" href="{{ $field->getLinkValue() }}" @if($field->isLinkBlank()) target="_blank" @endif>
                        {{ $field->getLinkName() }}
                    </a>
                @endif
            </dt>

            <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                {{ $slot }}

                @includeWhen($field->getHint(), 'moonshine::shared.hint', [
                    'hint' => $field->getHint()
                ])
            </dd>
        </div>
    </div>

    @error($field->name())
        @include('moonshine::base.form.shared.input-error', [
            'name' => $field->name(),
            'message' => $message
        ])
    @enderror
</div>
@endif
