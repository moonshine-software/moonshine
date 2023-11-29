{!! moonshineAssets()->toHtml() !!}

@stack('styles')

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    const translates = @js(__('moonshine::ui'));
</script>

<style>
    :root {
    @foreach (moonshineColors()->all() as $name => $value)
    --{{ $name }}:{{ $value }};
    @endforeach
    }
    :root.dark {
    @foreach (moonshineColors()->all(dark: true) as $name => $value)
        --{{ $name }}:{{ $value }};
    @endforeach
    }
</style>
