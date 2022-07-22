<style>
    input[type=range]::-webkit-slider-thumb {
        pointer-events: all;
        width: 24px;
        height: 24px;
        -webkit-appearance: none;
        /* @apply w-6 h-6 appearance-none pointer-events-auto; */
    }
</style>

<div class="">
    <div x-data="range_{{ $field->id() }}()" x-init="mintrigger(); maxtrigger()" class="relative max-w-xl w-full">
        <div>
            <input type="range"
                   step="{{ $field->step }}"
                   x-bind:min="min" x-bind:max="max"
                   x-on:input="mintrigger"
                   x-model="minValue"
                   class="absolute pointer-events-none appearance-none z-20 h-2 w-full opacity-0 cursor-pointer">

            <input type="range"
                   step="{{ $field->step  }}"
                   x-bind:min="min" x-bind:max="max"
                   x-on:input="maxtrigger"
                   x-model="maxValue"
                   class="absolute pointer-events-none appearance-none z-20 h-2 w-full opacity-0 cursor-pointer">

            <div class="relative z-10 h-2">
                <div class="absolute z-10 left-0 right-0 bottom-0 top-0 rounded-md bg-gray-200"></div>
                <div class="absolute z-20 top-0 bottom-0 rounded-md bg-purple" x-bind:style="'right:'+maxthumb+'%; left:'+minthumb+'%'"></div>
                <div class="absolute z-30 w-6 h-6 top-0 left-0 bg-purple rounded-full -mt-2 -ml-1" x-bind:style="'left: '+minthumb+'%'"></div>
                <div class="absolute z-30 w-6 h-6 top-0 right-0 bg-purple rounded-full -mt-2 -mr-3" x-bind:style="'right: '+maxthumb+'%'"></div>
            </div>

        </div>

        <div class="flex justify-between items-center py-5 text-black">
            <div>
                <input name="{{ $field->name() }}[{{ $field->fromField }}]" type="text" maxlength="5" x-on:input="mintrigger" x-model="minValue" class="px-3 py-2 border border-gray-200 rounded w-24 text-center">
            </div>
            <div>
                <input name="{{ $field->name() }}[{{ $field->toField }}]" type="text" maxlength="5" x-on:input="maxtrigger" x-model="maxValue" class="px-3 py-2 border border-gray-200 rounded w-24 text-center">
            </div>
        </div>
    </div>

    <script>
        function range_{{ $field->id()}}() {
            return {
                minValue: parseInt('{{ $field->formViewValue($item)[$field->fromField] ?? $field->min }}'),
                maxValue: parseInt('{{ $field->formViewValue($item)[$field->toField] ?? $field->max }}'),
                min: parseInt('{{ $field->min }}'),
                max: parseInt('{{ $field->max }}'),
                step: parseInt('{{ $field->step }}'),
                minthumb: 0,
                maxthumb: 0,

                mintrigger() {
                    this.minValue = Math.min(this.minValue, this.maxValue - this.step);
                    this.minthumb = ((this.minValue - this.min) / (this.max - this.min)) * 100;
                },

                maxtrigger() {
                    this.maxValue = Math.max(this.maxValue, this.minValue + this.step);
                    this.maxthumb = 100 - (((this.maxValue - this.min) / (this.max - this.min)) * 100);
                },
            }
        }
    </script>
</div>