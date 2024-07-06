@props([
    'drivers' => [],
    'attached',
    'profileMode' => false,
    'translates' => [],
])
@if($drivers !== [])
    <div class="social">
        <div class="social-divider">{{ $title ?? '' }}</div>
        <div class="social-list">
            @foreach($drivers as $driver)
                <a href="{{ $driver['route'] }}" class="social-item">
                    <img class="h-6 w-6"
                         src="{{ $driver['src'] }}"
                         alt="{{ $driver['name'] }}"
                    >
                </a>
            @endforeach
        </div>
    </div>

    @if($profileMode)
        @if($attached->isNotEmpty())
            <div class="social">
                <div class="social-divider">{{ $trans['linked'] }}</div>
                <div class="social-list">
                    @foreach($attached as $socials)
                        {{ $socials->driver }} - {{ $socials->identity }}
                    @endforeach
                </div>
            </div>
        @endif
    @endif
@endif
