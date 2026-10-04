@props([
    'escapeLabel' => null,
    'title' => '',
    'titleHtml' => null,
    'icon' => '',
    'progress' => false,
    'value' => 0,
    'simpleValue' => '',
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
        <div class="report-card-value">{!! $simpleValue !== '' ? $simpleValue : $value !!}</div>
        <h5 class="report-card-title">@if(! is_null($titleHtml)){{ $titleHtml }}@else<x-moonshine::display-text :value="$title" :escape="$escapeLabel" />@endif</h5>
    </div>
</div>
