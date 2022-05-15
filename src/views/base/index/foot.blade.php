<tr>
    <td class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider"
        colspan="{{ count($resource->indexFields())+2 }}"
    >
        <form action="{{ $resource->route("destroy", 1) }}" method="POST">
            @csrf
            @method("delete")

            <input name="ids" type="hidden" value="" class="actionBarIds">

            @if(in_array("delete", $resource->getActiveActions()))
                <button class="text-pink inline-block">
                    @include("moonshine::shared.icons.delete", ["size" => 6, "class" => "mr-2", "color" => "pink"])
                </button>
            @endif
        </form>


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

                        document.querySelector(".actionBarIds").value = values.join (";");
                    }
                };
            }
        </script>
    </td>
</tr>