@if($values && $columns)
    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
                <div>
                    <table class="min-w-full">
                        <thead class="bg-whiteblue dark:bg-purple">
                            <tr>
                                @foreach($columns as $name => $label)
                                    <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider"> {{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                            @foreach($values as $index => $data)
                                <tr>
                                    @foreach($columns as $name => $label)
                                        <td class="px-6 py-4 whitespace-no-wrap">{{ $data[$name] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif