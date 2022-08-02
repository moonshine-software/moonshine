<div class="flex justify-center items-center">
    <div class="relative max-w-xl w-full">
        <div class="flex justify-between items-center py-5">
            <div>
                @include("moonshine::fields.input", [
                    'element' => $element,
                    "valueKey" => 0,
                ])
            </div>
            <div>
                @include("moonshine::fields.input", [
                    'element' => $element,
                    "valueKey" => 1,
                ])
            </div>
        </div>

    </div>
</div>
