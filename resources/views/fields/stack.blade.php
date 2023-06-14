@if($indexView ?? false)
    @foreach($element->getFields()->indexFields() as $field)
        @if($element->hasLabels())
            <x-moonshine::divider :label="$field->label()" />
        @endif
        <div class="my-2">
            {!! $field->indexViewValue($item) !!}
        </div>
    @endforeach
@else
    <x-moonshine::resource-renderable
        :components="$element->getFields()"
        :item="$item"
        :resource="$resource"
        :container="false"
    />
@endif
