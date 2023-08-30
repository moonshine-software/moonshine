@props([
    'components' => []
])
<footer {{ $attributes->merge(['class' => 'layout-footer']) }}>
    <div class="flex flex-col flex-wrap items-center justify-between gap-y-4 gap-x-8 md:flex-row">
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}

        <div class="text-center text-2xs text-slate-500 md:text-left">
            &copy; 2021-{{ date('Y') }}. Made with ❤️ by
            <a href="https://cutcode.dev" class="font-semibold text-purple hover:text-pink" target="_blank">CutCode</a>
        </div>

        <nav class="flex flex-wrap justify-center gap-x-4 gap-y-2 md:justify-start lg:gap-x-6">
            <a href="https://github.com/moonshine-software/moonshine"
               class="text-2xs text-slate-500 hover:text-purple"
               target="_blank">
                Documentation
            </a>

            <a href="https://moonshine.cutcode.dev"
               class="text-2xs text-slate-500 hover:text-purple"
               target="_blank"
            >
                GitHub
            </a>
        </nav>
    </div>
</footer>
