@props([
    'tabs',
    'contents',
    'active',
    'justifyAlign' => 'start',
    'vertical' => false,
])
@if($tabs)
    <!-- Tabs -->
    <div {{ $attributes->class(['tabs']) }}
        :class="{'tabs-vertical': {{ $vertical ? 'true' : 'false'}} }"
        x-data="tabsCollapse(
            {{ json_encode($tabs) }},
            '{{ $active ?? array_key_first($tabs) }}'
        )"
        x-init="init()"
    >
        <!-- Collapse tabs buttons -->
        <div class="overflow-hidden">
            <button type="button"
                @click.prevent="toggle()"
                :class="{ '_is-active': collapse }"
                class="accordion-btn btn w-full tabs-collapse"
                type="button"
            >
                <div x-html="activeTitle" class="tabs-title"></div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

        <!-- Tabs Buttons -->
        <ul x-cloak x-show="collapse"
            @class(['tabs-list', 'justify-' . $justifyAlign])>
            @foreach($tabs as $tabId => $tabContent)
                <li class="tabs-item">
                    <button
                        @click.prevent="clickingTab('{{ $tabId }}')"
                        :class="{
                            '_is-active': activeTab === '{{ $tabId }}',
                        }"
                        class="tabs-button"
                        type="button"
                    >
                        {!! $tabContent !!}
                    </button>
                </li>
            @endforeach
        </ul>
        <!-- END: Tabs Buttons -->
        </div>

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
