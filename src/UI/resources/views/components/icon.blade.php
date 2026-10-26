<div {{ $wrapperAttributes }}>
    @if($hasSlot)
        {!! $slot !!}
    @elseif($iconView)
        @include($iconView)
    @endif
</div>
