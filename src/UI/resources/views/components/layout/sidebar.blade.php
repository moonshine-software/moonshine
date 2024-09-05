@props([
    'components' => [],
    'collapsed' => false,
    'collapseAttributes',
    'translates' => [],
])
<aside {{ $attributes->merge(['class' => 'layout-menu']) }}
       :class="minimizedMenu && '_is-minimized'"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}

    @if($collapsed)
        <div {{ $collapseAttributes->merge(['class' => 'border-t border-dark-200']) }}>
            <ul class="menu-inner mt-2">
                <li class="menu-inner-item hidden xl:block">
                    <button
                        type="button"
                        x-data="navTooltip"
                        @mouseenter="toggleTooltip()"
                        @click.prevent="minimizedMenu = ! minimizedMenu"
                        class="menu-inner-button outline-none"
                    >
                        <svg x-show="!minimizedMenu"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor"
                             class="h-6 w-6"
                        >
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M11.25 9l-3 3m0 0l3 3m-3-3h7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>

                        <svg x-show="minimizedMenu"
                             style="display: none"
                             xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor"
                             class="h-6 w-6"
                        >
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M12.75 15l3-3m0 0l-3-3m3 3h-7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>

                        <span class="menu-inner-text" x-show="!minimizedMenu">
                            {{ $translates['collapse_menu'] ?? '' }}
                        </span>
                    </button>
                </li>
            </ul>
        </div>
    @endif
</aside>
