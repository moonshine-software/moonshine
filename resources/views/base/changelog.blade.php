@if($item->changeLogs && $item->changeLogs->isNotEmpty())
    <div class="my-6 text-lg">Последнии 5 изменений</div>

    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full">
                    <thead class="bg-whiteblue dark:bg-purple">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium  uppercase tracking-wider">
                                Пользователь
                            </th>
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                Изменения
                            </th>
                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                Дата
                            </th>
                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                    @foreach($item->changeLogs->take(5) as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                @include('moonshine::shared.badge', [
                                    'color' => 'purple',
                                    'value' => $log->moonshineUser->name
                                ])
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-black">
                                <table>
                                    <thead class="bg-whiteblue dark:bg-purple">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                                Поле
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                                До
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider">
                                                После
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                                        @foreach($log->states_after as $changedField => $changedValue)
                                            @if($resource->getField($changedField) && !$resource->getField($changedField) instanceof \Leeto\MoonShine\Fields\Json)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-no-wrap">
                                                        {{ $resource->getField($changedField) ? $resource->getField($changedField)->label() : $changedField }}
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-no-wrap">
                                                        {{ is_array($log->states_before[$changedField]) ? json_encode($log->states_before[$changedField]) : $log->states_before[$changedField] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-no-wrap">
                                                        {{ is_array($changedValue) ? json_encode($changedValue) : $changedValue }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif