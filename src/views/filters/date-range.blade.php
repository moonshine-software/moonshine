<div class="flex justify-center items-center">
    <div class="relative max-w-xl w-full">
        <div class="flex justify-between items-center py-5">
            <div>
                @include("moonshine::fields.input", [
                    "field" => $field,
                    "resource" => $resource,
                    "item" => $resource->getModel(),
                    "valueKey" => 0,
                ])
            </div>
            <div>
                @include("moonshine::fields.input", [
                    "field" => $field,
                    "resource" => $resource,
                    "item" => $resource->getModel(),
                    "valueKey" => 1,
                ])
            </div>
        </div>

    </div>
</div>