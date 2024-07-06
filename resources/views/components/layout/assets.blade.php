@props([
    'colors' => '',
    'assets' => '',
    'translates' => [],
])

@stack('styles')

{!! $colors !!}
{!! $assets !!}

{{ $slot ?? '' }}

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    const translates = @js($translates);
</script>
