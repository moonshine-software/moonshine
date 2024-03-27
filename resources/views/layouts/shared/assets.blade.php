{!! moonshineAssets()->toHtml() !!}
{!! moonshineColors()->toHtml() !!}

@stack('styles')

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    const translates = @js(__('moonshine::ui'));
</script>
