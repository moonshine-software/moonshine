@props([
    'toggleMethod' => 'toggleSidebar',
    'toggleState' => 'isSidebarOpen',
    'sidebar' => false,
    'topbar' => false,
    'mobileBar' => false,
])

<button
    type="button"
    {{ $attributes->merge([
        'class'          => 'btn-burger btn-fit',
        '@click.prevent' => '$store.menu.' . $toggleMethod . '()',
    ]) }}
>
    <svg x-show="!$store.menu.{{ $toggleState }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
    </svg>
    <svg x-cloak x-show="$store.menu.{{ $toggleState }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
    </svg>
</button>
