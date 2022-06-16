<tr>
    <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
        <input type="checkbox" @change="actionBar('main')" class="actionBarCheckboxMain" value="1" />
    </th>

    @foreach($resource->indexFields() as $field)
        <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
            {{ $field->label() }}

            @if($field->isSortable())
                <a
                    href="{{ request()->fullUrlWithQuery([
                        'order[field]' => $field->name(),
                        'order[type]' => (request()->has("order.type") && request("order.type") == "asc" ? "desc" : "asc")
                    ]) }}"
                    class="inline-block align-middle"
                >
                    <svg class="fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif
        </th>
    @endforeach

    <th class="px-6 py-3"></th>
</tr>