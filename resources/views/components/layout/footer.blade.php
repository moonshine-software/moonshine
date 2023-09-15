@props([
    'components' => [],
    'copyright' => '',
    'menu' => []
])
<footer {{ $attributes->merge(['class' => 'layout-footer']) }}>
    <div class="flex flex-col flex-wrap items-center justify-between gap-y-4 gap-x-8 md:flex-row">
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}

        <div class="text-center text-2xs text-slate-500 md:text-left">
            {!! $copyright !!}
        </div>

        @if(!empty($menu))
            <nav class="flex flex-wrap justify-center gap-x-4 gap-y-2 md:justify-start lg:gap-x-6">
                @foreach($menu as $link => $label)
                    <a href="{{ $link }}"
                       class="text-2xs text-slate-500 hover:text-primary"
                       target="_blank">
                        {!! $label !!}
                    </a>
                @endforeach
            </nav>
        @endif
    </div>
</footer>
