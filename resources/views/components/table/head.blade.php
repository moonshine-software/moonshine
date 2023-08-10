@props([
    'fields',
    'actions',
])

@if($actions->isNotEmpty())
    <th class="w-10 text-center">
        <x-moonshine::form.input type="checkbox"
             @change="actions('all')"
             class="actionsAllChecked"
             value="1"
        />
    </th>
@endif

@foreach($fields as $field)
    <th>
        <div class="flex items-baseline gap-x-1">
            {{ $field->label() }}

            @if($field->isSortable())
                <a href="{{ $field->sortQuery() }}" class="shrink-0" @click.prevent="canBeAsync">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" fill-opacity="{{ $field->sortDirection('asc') && $field->sortActive() ? '1' : '.5' }}" d="m11.47,4.72a0.75,0.75 0 0 1 1.06,0l3.75,3.75a0.75,0.75 0 0 1 -1.06,1.06l-3.22,-3.22l-3.22,3.22a0.75,0.75 0 0 1 -1.06,-1.06l3.75,-3.75z" clip-rule="evenodd"></path>
                        <path fill-rule="evenodd" fill-opacity="{{ $field->sortDirection('desc') && $field->sortActive() ? '1' : '.5' }}" d="m12.53,4.72zm-4.81,9.75a0.75,0.75 0 0 1 1.06,0l3.22,3.22l3.22,-3.22a0.75,0.75 0 1 1 1.06,1.06l-3.75,3.75a0.75,0.75 0 0 1 -1.06,0l-3.75,-3.75a0.75,0.75 0 0 1 0,-1.06z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            @endif
        </div>
    </th>
@endforeach

<th></th>
