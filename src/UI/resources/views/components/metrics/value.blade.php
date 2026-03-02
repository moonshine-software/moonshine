@props([
    'title' => '',
    'titleRaw' => false,
    'escapeUi' => false,
    'icon' => '',
    'progress' => false,
    'value' => 0,
    'simpleValue' => '',
    'simpleValueRaw' => false,
])
<div {{ $attributes->merge(['class' => 'report-card']) }}>
    @if($icon)
        <div class="report-card-heading">
            {!! $icon !!}
        </div>
    @endif

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
        <div class="report-card-value">
            @if(! $escapeUi || $simpleValueRaw)
                {!! $simpleValue !== '' ? $simpleValue : $value !!}
            @else
                {{ $simpleValue !== '' ? $simpleValue : $value }}
            @endif
        </div>
        <h5 class="report-card-title">
            @if(! $escapeUi || $titleRaw)
                {!! $title !!}
            @else
                {{ $title }}
            @endif
        </h5>
    </div>
</div>
