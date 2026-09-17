@props([
    'escapeLabel' => moonshine()->getConfig()->isEscapeLabel(),
    'tabs' => [],
    'active' => null,
    'justifyAlign' => 'start',
    'isVertical' => false,
])
@if($tabs !== [])
    <!-- Tabs -->
    <div {{ $attributes->class(['tabs']) }}
        x-data="tabs(
            @js((string) ($active ?? array_key_first($tabs))),
            @js($isVertical)
        )"
    >
        <!-- Tabs Buttons -->
        <ul @class(['tabs-list', 'justify-' . $justifyAlign])>
            @foreach($tabs as $tab)
                <li class="tabs-item">
                    <button {!! $tab['labelAttributes'] !!}
                            type="button"
                    >
                        {!! $tab['icon'] !!}
                        {!! ($tab['escapeLabel'] ?? $escapeLabel) ? e($tab['label']) : $tab['label'] !!}
                    </button>
                </li>
            @endforeach
        </ul>
        <!-- END: Tabs Buttons -->

        <!-- Tabs content -->
        <div class="tabs-content">
            @foreach($tabs as $tab)
                <div {!! $tab['attributes'] !!}>
                    <div class="tabs-body space-elements">
                        {!! $tab['content'] !!}
                    </div>
                </div>
            @endforeach
        </div>
        <!-- END: Tabs content -->
    </div>
    <!-- END: Tabs -->
@endif
