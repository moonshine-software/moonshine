{!! moonshineAssets()->css() !!}

@stack('styles')

{!! moonshineAssets()->js() !!}

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    const translates = @js(__('moonshine::ui'));
</script>

<style>
    :root {
    @foreach (moonshineAssets()->getColors() as $name => $value)
    --{{ $name }}:{{ $value }};
    @endforeach
    }
    :root[class="dark"] {
    @foreach (moonshineAssets()->getColors(dark: true) as $name => $value)
        --{{ $name }}:{{ $value }};
    @endforeach
    }
</style>
