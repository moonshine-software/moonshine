@if($isButton)
    <button {{ $tagAttributes }}>
        {!! $icon !!}
        {!! $slot !!}
        {!! $label !!}
        @if($hasBadge)
            <span class="badge">{{ $badge }}</span>
        @endif
    </button>
@else
    <a {{ $tagAttributes }}>
        {!! $icon !!}
        {!! $slot !!}
        {!! $label !!}
        @if($hasBadge)
            <span class="badge">{{ $badge }}</span>
        @endif
    </a>
@endif

@if($hasComponent)
    <template x-teleport="body">
        {!! $component !!}
    </template>
@endif
