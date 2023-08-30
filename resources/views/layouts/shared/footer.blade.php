<!-- Footer -->
@if(!empty(config('moonshine.footer.copyright')) || !empty(config('moonshine.footer.nav')))
    <footer class="layout-footer">
        <div class="flex flex-col flex-wrap items-center justify-between gap-y-4 gap-x-8 md:flex-row">
            @if(!empty(config('moonshine.footer.copyright')))
                <div class="text-center text-2xs text-slate-500 md:text-left">
                    &copy; 2021-{{ date('Y') }}. {!! config('moonshine.footer.copyright') !!}
                </div>
            @endif
            @if(!empty(config('moonshine.footer.nav')))
                @if(is_string(config('moonshine.footer.nav')))
                    <nav class="text-center text-2xs text-slate-500 md:text-left">
                        {!! config('moonshine.footer.nav') !!}
                    </nav>
                @elseif(is_iterable(config('moonshine.footer.nav')))
                    <nav class="flex flex-wrap justify-center gap-x-4 gap-y-2 md:justify-start lg:gap-x-6">
                        @foreach(config('moonshine.footer.nav') as $url => $text)
                            <a href="{{ $url }}" class="text-2xs text-slate-500 hover:text-purple"
                               target="_blank">{!! $text !!}</a>
                        @endforeach
                    </nav>
                @endif
            @endif
        </div>
    </footer>
@endif
<!-- END: Footer -->
