@props([
    'min' => 0,
    'max' => 1000000,
    'fromAttributes' => $attributes,
    'toAttributes' => $attributes,
    'fromName',
    'toName',
    'fromValue',
    'toValue',
    'fromField' => $fromName,
    'toField' => $toName
])
<div {{ $attributes->class(['form-group-range'])->only('class') }}>
    <div x-data="range(@js($fromValue ?? $min), @js($toValue ?? $max))"
         x-init="mintrigger(); maxtrigger()"
         data-min="{{ $attributes->get('min', $min) }}"
         data-max="{{ $attributes->get('max', $max) }}"
         data-step="{{ $attributes->get('step', 1) }}"
    >
        <div class="form-group-range-wrapper">
            <x-moonshine::form.input
                type="range"
                step="{{ $attributes->get('step', 1) }}"
                x-bind:min="min"
                x-bind:max="max"
                x-on:input="mintrigger"
                x-model="minValue"
                :disabled="$attributes->get('readonly')"
                :attributes="$fromAttributes->except(['type'])"
            />

            <x-moonshine::form.input
                type="range"
                step="{{ $attributes->get('step', 1) }}"
                x-bind:min="min"
                x-bind:max="max"
                x-on:input="maxtrigger"
                x-model="maxValue"
                :disabled="$attributes->get('readonly')"
                :attributes="$toAttributes->except(['type'])"
            />


            <div class="form-range-slider">
                <div class="form-range-tracker"></div>
                <div class="form-range-connect" x-bind:style="'right:'+maxthumb+'%; left:'+minthumb+'%'"></div>
            </div>
        </div>

        <div class="form-group-range-fields">
            <x-moonshine::form.input
                type="number"
                maxlength="5"
                step="{{ $attributes->get('step', 1) }}"
                x-bind:min="min"
                x-bind:max="max"
                x-on:input="mintrigger"
                x-model="minValue"
                :attributes="$fromAttributes->merge([
                    'name' => $fromName,
                ])"
                value="{{ $fromValue }}"
            />

            <x-moonshine::form.input
                type="number"
                step="{{ $attributes->get('step', 1) }}"
                maxlength="5"
                x-bind:min="min"
                x-bind:max="max"
                x-on:input="maxtrigger"
                x-model="maxValue"
                :attributes="$toAttributes->merge([
                    'name' => $toName,
                ])"
                value="{{ $toValue }}"
            />
        </div>
    </div>
</div>
