@if($element->label())
    <h5 class="text-md font-medium">
        <a href="{{ $element->getResource()->route('index') }}">
            {{ $element->label() }}
        </a>
    </h5>
@endif

@include('moonshine::crud.shared.table', [
    'resource' => $element->getResource(),
    'resources' => $element->items(),
])

