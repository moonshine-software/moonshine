@props([
    'uniqueId' => 'slider',
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
    <div x-data="range({{ '`'.$fromValue.'`,`'.$toValue.'`' }})"
         x-init="mintrigger(); maxtrigger()"
         data-min="{{ $attributes->get('min', 0) }}"
         data-max="{{ $attributes->get('max', 1000) }}"
         data-step="{{ $attributes->get('step', 1) }}"
         class="form-group-range-wrapper"
    >
        <div>
            <x-moonshine::form.input
                type="range"
                step="{{ $attributes->get('step', 1) }}"
                x-bind:min="min"
                x-bind:max="max"
                x-on:input="mintrigger"
                x-model="minValue"
                :attributes="$fromAttributes->except(['type'])->merge([
                    'class' => 'form-range-input',
                ])"
            />

            <x-moonshine::form.input
                type="range"
                step="{{ $attributes->get('step', 1) }}"
                x-bind:min="min"
                x-bind:max="max"
                x-on:input="maxtrigger"
                x-model="maxValue"
                :attributes="$toAttributes->except(['type'])->merge([
                    'class' => 'form-range-input',
                ])"
            />

            <div class="form-range-slider">
                <div class="form-range-tracker"></div>
                <div class="form-range-connect" x-bind:style="'right:'+maxthumb+'%; left:'+minthumb+'%'"></div>
                <div class="form-range-thumb form-range-thumb-left" x-bind:style="'left: '+minthumb+'%'"></div>
                <div class="form-range-thumb form-range-thumb-right" x-bind:style="'right: '+maxthumb+'%'"></div>
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
