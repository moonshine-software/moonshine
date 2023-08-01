@props([
    'rows',
    'actions'
])
<td colspan="{{ count($rows)+2 }}">
    <div class="flex items-center gap-4">
        <x-moonshine::table.actions
            :actions="$actions"
        />
    </div>
</td>