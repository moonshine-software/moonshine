@props([
    'fields',
    'actions',
    'rows',
    'asyncUrl' => null,
    'preview' => false,
])

@if(!$preview && $actions->isNotEmpty())
    <th class="w-10 text-center">
        <x-moonshine::form.input type="checkbox"
             autocomplete="off"
             @change="actions('all', $id('table-component'))"
             class="actionsAllChecked"
             ::class="$id('table-component') + '-actionsAllChecked'"
             value="1"
        />
    </th>
@endif

@foreach($fields as $field)
    <th data-column-selection="{{ $field->id() }}">
        @if(!$preview && $field->isSortable())
            <a href="{{ $field->sortQuery($asyncUrl) }}" @if(!is_null($asyncUrl))@click.prevent="asyncRequest" @endif class="flex items-baseline gap-x-1">
                {{ $field->label() }}
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" fill-opacity="{{ $field->sortDirection('asc') && $field->sortActive() ? '1' : '.4' }}" d="m11.47,4.72a0.75,0.75 0 0 1 1.06,0l3.75,3.75a0.75,0.75 0 0 1 -1.06,1.06l-3.22,-3.22l-3.22,3.22a0.75,0.75 0 0 1 -1.06,-1.06l3.75,-3.75z" clip-rule="evenodd"></path>
                    <path fill-rule="evenodd" fill-opacity="{{ $field->sortDirection('desc') && $field->sortActive() ? '1' : '.4' }}" d="m12.53,4.72zm-4.81,9.75a0.75,0.75 0 0 1 1.06,0l3.22,3.22l3.22,-3.22a0.75,0.75 0 1 1 1.06,1.06l-3.75,3.75a0.75,0.75 0 0 1 -1.06,0l-3.75,-3.75a0.75,0.75 0 0 1 0,-1.06z" clip-rule="evenodd"></path>
                </svg>
            </a>
        @else
            {{ $field->label() }}
        @endif
    </th>
@endforeach

@if(!$preview)
    <th></th>
@endif
