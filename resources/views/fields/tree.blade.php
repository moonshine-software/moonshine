<div x-data="tree_{{ $field->id() }}()" x-init="() => { initTree() }">
    {!! $field->buildTreeHtml($item) !!}

    <script>
        function tree_{{ $field->id() }}() {
            return {
                checked: @json($field->formViewValue($item)->modelKeys()),
                ids: @json($field->ids()),
                initTree() {
                    var refs = this.$refs;
                    var checked = this.checked;

                    this.ids.forEach(function (id) {
                        var input = refs["item_" + id].querySelector("input");

                        checked.forEach(function (c) {
                            if(c == input.value) {
                                input.checked = true;
                            }
                        });
                    });
                }
            }
        }
    </script>
</div>
