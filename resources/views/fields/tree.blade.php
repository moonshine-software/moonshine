<div x-data="tree_{{ $element->id() }}()" x-init="() => { initTree() }">
    {!! $element->buildTreeHtml($item) !!}

    <script>
        function tree_{{ $element->id() }}() {
            return {
                checked: @json($element->formViewValue($item)->modelKeys()),
                ids: @json($element->ids()),
                initTree() {
                    const refs = this.$refs;
                    const checked = this.checked;

                    this.ids.forEach(function (id) {
                        const input = refs["item_" + id].querySelector("input");

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
