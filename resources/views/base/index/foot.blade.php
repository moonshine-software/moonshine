@if($resource->isMassAction())
<tr>
    <td class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider"
        colspan="{{ count($resource->indexFields())+2 }}"
    >

        <div class="flex items-center">
            @foreach($resource->bulkActions() as $index => $action)
                <form action="{{ $resource->route("bulk", query: ['index' => $index]) }}" method="POST">
                    @csrf

                    @if($resource->isRelatable())
                        <input type="hidden" name="relatable_mode" value="1">
                    @endif

                    @if(request()->routeIs('*.query-tag'))
                        <input type="hidden" name="redirect_back" value="1">
                    @endif

                    <input name="ids" type="hidden" value="" class="actionBarIds">

                    <button type="submit" class="text-pink inline-block" title="{{ $action->label() }}">
                        {{ $action->getIcon(6, 'pink', 'mr-2') }}
                    </button>
                </form>
            @endforeach

            @if($resource->can('massDelete') && in_array('delete', $resource->getActiveActions()))
                <x-moonshine::modal>
                    {{ trans('moonshine::ui.confirm_delete') }}

                    <x-slot name="icon">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </x-slot>

                    <x-slot name="title">{{ trans('moonshine::ui.deleting') }}</x-slot>

                    <x-slot name="buttons">
                        <div class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                            <form action="{{ $resource->route("destroy", 1) }}" method="POST">
                                @csrf

                                @if($resource->isRelatable())
                                    <input type="hidden" name="relatable_mode" value="1">
                                @endif

                                @if(request()->routeIs('*.query-tag'))
                                    <input type="hidden" name="redirect_back" value="1">
                                @endif

                                @method("delete")

                                <input name="ids" type="hidden" value="" class="actionBarIds">

                                <button type="submit"
                                        class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-red-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-red-500 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                    {{ trans('moonshine::ui.confirm') }}
                                </button>
                            </form>
                        </div>
                        <div class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                            <button x-on:click="isOpen = false" type="button"
                                    class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-purple focus:shadow-outline-purple transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                {{ trans('moonshine::ui.cancel') }}
                            </button>
                        </div>
                    </x-slot>

                    <x-slot name="outerHtml">
                        <button x-on:click="isOpen = !isOpen" type="button" class="text-pink inline-block">
                            @include("moonshine::shared.icons.delete", ["size" => 6, "class" => "mr-2", "color" => "pink"])
                        </button>
                    </x-slot>
                </x-moonshine::modal>
            @endif
        </div>

        <script>
            function actionBarHandler() {
                return {
                    actionBarOpen : false,
                    actionBarCheckboxMain : false,
                    actionBar(type) {
                        if(document.querySelector('.actionBarCheckboxMain:checked') != null) {
                            this.actionBarCheckboxMain = true;
                        } else {
                            this.actionBarCheckboxMain = false;
                        }

                        var checkboxes = document.querySelectorAll('.actionBarCheckboxRow');
                        var values = [];

                        for(var i=0, n=checkboxes.length;i<n;i++) {
                            if(type == 'main') {
                                checkboxes[i].checked = this.actionBarCheckboxMain;
                            }

                            if(checkboxes[i].checked && checkboxes[i].value) {
                                values.push(checkboxes[i].value);
                            }
                        }

                        if(document.querySelector('.actionBarCheckboxRow:not(:checked)') != null) {
                            document.querySelector('.actionBarCheckboxMain').checked = false;
                        } else {
                            document.querySelector('.actionBarCheckboxMain').checked = true;
                        }

                        if(document.querySelector('.actionBarCheckboxRow:checked') != null) {
                            this.actionBarOpen = true;
                        } else {
                            this.actionBarOpen = false;
                        }

                        var allIdsInput = document.querySelectorAll('.actionBarIds');

                        for(var i=0, n=allIdsInput.length;i<n;i++) {
                            allIdsInput[i].value = values.join (";");
                        }
                    }
                };
            }
        </script>
    </td>
</tr>
@endif
