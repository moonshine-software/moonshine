@props([
    'title' => '',
    'icon' => '',
    'progress' => false,
    'value' => 0,
    'simpleValue' => '',
])
<div {{ $attributes->merge(['class' => 'report-card']) }}>
    <div class="report-card-heading">
        {!! $icon !!}
    </div>

    @if($progress)
        <x-moonshine::progress-bar
            color="primary"
            :radial="false"
            :value="$value"
        >
            {{ $value }}%
        </x-moonshine::progress-bar>
    @endif

    <div class="report-card-body">
        <div class="report-card-value">{{ $simpleValue }}</div>
        <h5 class="report-card-title">{!! $title !!}</h5>
    </div>
</div>
