@props([
    'drivers' => [],
    'attached',
    'profileMode' => false,
])
@if($drivers !== [])
    <div class="social">
        <div class="social-divider">{{ $title ?? '' }}</div>
        <div class="social-list">
            @foreach($drivers as $driver => $src)
                <a href="{{ route('moonshine.socialite.redirect', $driver) }}" class="social-item">
                    <img class="h-6 w-6"
                         src="{{ moonshineAssets()->asset($src) }}"
                         alt="{{ $driver }}"
                    >
                </a>
            @endforeach
        </div>
    </div>

    @if($profileMode)
        @if($attached->isNotEmpty())
            <div class="social">
                <div class="social-divider">@lang('moonshine::ui.resource.linked_socialite')</div>
                <div class="social-list">
                    @foreach($attached as $socials)
                        {{ $socials->driver }} - {{ $socials->identity }}
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@endif
