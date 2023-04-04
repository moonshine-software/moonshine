@section('title', $title)

<h1 class="{{ isset($subTitle) && $subTitle ?: 'mb-6' }} truncate text-md font-medium">
    {{ $title }}
</h1>

@if($subTitle ?? false)
    <div class="pt-2 mb-6">{!! $subTitle !!}</div>
@endif
