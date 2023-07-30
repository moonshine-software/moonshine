@if($indexView ?? false)
    @foreach($element->getFields()->indexFields() as $field)
        @if($element->hasLabels())
            <x-moonshine::divider :label="$field->label()" />
        @endif
        <div class="my-2">
            {!! $field->preview() !!}
        </div>
    @endforeach
@else
    <x-moonshine::fields-group
        :components="$element->getFields()"
        :container="false"
    />
@endif
