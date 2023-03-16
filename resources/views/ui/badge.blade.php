@if($value !== false)
    <span class="badge badge-{{ $color }}">
        {!! $value !!}
    </span>
@else
    &mdash;
@endif
