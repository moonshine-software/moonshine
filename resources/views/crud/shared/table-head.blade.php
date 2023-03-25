@if($resource->hasMassAction())
    <th class="w-10 text-center">
        <x-moonshine::form.input type="checkbox"
             @change="actions('all')"
             class="actionsAllChecked"
             value="1"
        />
    </th>
@endif

@foreach($resource->getFields()->indexFields() as $field)
    <th>
        <div class="flex items-baseline gap-x-1">
            {{ $field->label() }}
            @if(!$resource->isPreviewMode() && !$resource->isRelatable() && $field->isSortable())
            <a href="{{ request()->fullUrlWithQuery([
                    'order[field]' => $field->name(),
                    'order[type]' => (request()->has("order.type") && request("order.type") == "asc" ? "desc" : "asc")
                ]) }}"
               class="shrink-0"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" fill-opacity="{{ (request()->has("order.type") && request("order.field") == $field->name() && request("order.type") == "asc") ? '1' : '.5' }}" d="m11.47,4.72a0.75,0.75 0 0 1 1.06,0l3.75,3.75a0.75,0.75 0 0 1 -1.06,1.06l-3.22,-3.22l-3.22,3.22a0.75,0.75 0 0 1 -1.06,-1.06l3.75,-3.75z" clip-rule="evenodd"></path>
                    <path fill-rule="evenodd" fill-opacity="{{ (request()->has("order.type") && request("order.field") == $field->name() && request("order.type") == "desc") ? '1' : '.5' }}" d="m12.53,4.72zm-4.81,9.75a0.75,0.75 0 0 1 1.06,0l3.22,3.22l3.22,-3.22a0.75,0.75 0 1 1 1.06,1.06l-3.75,3.75a0.75,0.75 0 0 1 -1.06,0l-3.75,-3.75a0.75,0.75 0 0 1 0,-1.06z" clip-rule="evenodd"></path>
                </svg>
            </a>
            @endif
        </div>
    </th>
@endforeach

@if(!$resource->isPreviewMode())
    <th></th>
@endif

