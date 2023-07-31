<td colspan="{{ count($rows)+2 }}"
>
    <div class="flex items-center gap-4">
        @include('moonshine::crud.shared.table-row-actions', [
            'actions' => $bulkButtons
        ])
    </div>
</td>