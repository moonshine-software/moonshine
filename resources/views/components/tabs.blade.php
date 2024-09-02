@props([
    'tabs',
    'contents' => [],
    'active' => null,
    'justifyAlign' => 'start',
    'isVertical' => false,
])

@if($tabs)
    @php
        if ($contents === []) {
            $tabs = collect($tabs)
                ->mapWithKeys(fn($value, $name) => [Str::camel($name) => $value])
                ->all();

            $contents = collect($__laravel_slots ?? [])
                ->mapWithKeys(fn($contentSlot, $name) => [Str::camel($name) => $contentSlot])
                ->filter(fn($contentSlot, $name) => array_key_exists($name, $tabs))
                ->all();
        }
    @endphp

    <!-- Tabs -->
    <div {{ $attributes->class(['tabs']) }}
        x-data="tabs(
            '{{ $active ?? array_key_first($tabs) }}',
            {{ $isVertical ? 'true' : 'false' }}
        )"
    >
        <!-- Tabs Buttons -->
        <ul @class(['tabs-list', 'justify-' . $justifyAlign])>
            @foreach($tabs as $tabId => $tabContent)
                <li class="tabs-item">
                    <button
                        @click.prevent="setActiveTab(`{{ $tabId }}`)"
                        :class="{ '_is-active': activeTab === '{{ $tabId }}' }"
                        class="tabs-button"
                        type="button"
                    >
                        {!! $tabContent !!}
                    </button>
                </li>
            @endforeach
        </ul>
        <!-- END: Tabs Buttons -->

        <!-- Tabs content -->
        <div class="tabs-content">
            @foreach($contents as $tabId => $tabContent)
                <div
                    :class="activeTab === '{{ $tabId }}' ? 'block' : 'hidden'"
                    class="tab-panel"
                    @set-active-tab="setActiveTab(`{{ $tabId }}`)"
                >
                    <div class="tabs-body">
                        {!! $tabContent !!}
                    </div>
                </div>
            @endforeach
        </div>
        <!-- END: Tabs content -->
    </div>
    <!-- END: Tabs -->
@endif
