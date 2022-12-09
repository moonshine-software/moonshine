<tr>
    <td class="px-6 py-3 text-left text-xs leading-4 font-medium uppercase tracking-wider"
        colspan="{{ count($resource->indexFields())+2 }}"
    >

        <div class="flex items-center">
            @foreach($resource->bulkActions() as $index => $action)
                <form action="{{ $resource->route("bulk", query: ['index' => $index]) }}" method="POST">
                    @csrf

                    <input name="ids" type="hidden" value="" class="actionBarIds">

                    <button type="submit" class="text-pink inline-block" title="{{ $action->label() }}">
                        {{ $action->getIcon(6, 'pink', 'mr-2') }}
                    </button>
                </form>
            @endforeach

            <form action="{{ $resource->route("destroy", 1) }}" method="POST">
                @csrf
                @method("delete")

                <input name="ids" type="hidden" value="" class="actionBarIds">

                @if($resource->can('massDelete') && in_array('delete', $resource->getActiveActions()))
                    <button class="text-pink inline-block">
                        @include("moonshine::shared.icons.delete", ["size" => 6, "class" => "mr-2", "color" => "pink"])
                    </button>
                @endif
            </form>
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
