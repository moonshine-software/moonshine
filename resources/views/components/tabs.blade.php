@props([
    'tabs',
    'contents',
    'active',
    'justifyAlign' => 'start',
    'vertical' => false,
    'activationWidth'
])
@if($tabs)
    <!-- Tabs -->
    <div {{ $attributes->class(['tabs']) }}
        x-data="tabsModule(
            {{ json_encode($tabs) }},
            '{{ $active ?? array_key_first($tabs) }}',
            {{ json_encode($vertical) }}
        )"
        x-init="$nextTick(() => { initTabs() })"

    >
        <!-- Tabs Buttons -->
        <ul @class(['tabs-list', 'justify-' . $justifyAlign])>
            @foreach($tabs as $tabId => $tabContent)
                <li class="tabs-item">
                    <button
                        @click.prevent="clickingTab('{{ $tabId }}')"
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
                <div x-show="activeTab === '{{ $tabId }}'" class="tab-panel" style="display: none">
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

