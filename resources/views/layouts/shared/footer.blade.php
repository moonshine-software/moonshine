<!-- Footer -->
<footer class="layout-footer">
    <div class="flex flex-col flex-wrap items-center justify-between gap-y-4 gap-x-8 md:flex-row">
        <div class="text-center text-2xs text-slate-500 md:text-left">
            &copy; 2021-{{ date('Y') }}. {!! config('moonshine.footer.copyright') !!}
        </div>
        <nav class="flex flex-wrap justify-center gap-x-4 gap-y-2 md:justify-start lg:gap-x-6">
            @foreach(config('moonshine.footer.nav') as $url=>$text)
                <a href="{{ $url }}" class="text-2xs text-slate-500 hover:text-purple" target="_blank">{!! $text !!}</a>
            @endforeach
            <a href="https://moonshine.cutcode.dev/" class="text-2xs text-slate-500 hover:text-purple" target="_blank">{!! __('Documentation') !!}</a>
        </nav>
    </div>
</footer>
<!-- END: Footer -->
