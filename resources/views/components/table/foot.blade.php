@props([
    'rows',
    'actions'
])
<td colspan="{{ count($rows)+2 }}"
    ::class="$id('table-component') + '-bulkActions'"
>
    <div class="flex items-center gap-4">
        <x-moonshine::table.actions
            :actions="$actions"
        />
    </div>
</td>
