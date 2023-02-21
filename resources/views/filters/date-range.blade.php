<div x-data="date_range_{{ $field->id() }}()" class="relative max-w-xl w-full">
    <div class="flex justify-between items-center">
        <div>
            <input
                name="{{ $field->name() }}[from]"
                type="date"
                x-bind:max="toDate"
                x-model="fromDate"
                style="width: 130px;"
                class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-2 appearance-none leading-normal"
            />
        </div>
        <div>
            <input
                name="{{ $field->name() }}[to]"
                type="date"
                x-model="toDate"
                x-bind:min="fromDate"
                style="width: 130px;"
                class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-2 appearance-none leading-normal"
            />
        </div>
    </div>
</div>

<script>
    function date_range_{{ $field->id()}}() {
        return {
            fromDate: '{{ $field->formViewValue($item)['from'] ?? '' }}',
            toDate: '{{ $field->formViewValue($item)['to'] ?? '' }}',
        }
    }
</script>
