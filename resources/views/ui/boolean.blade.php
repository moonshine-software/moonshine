@if(is_bool($value))
    <div class="h-2 w-2 rounded-full bg-{{ $value ? 'green' : 'red' }}-500"></div>
@endif
