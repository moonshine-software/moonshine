@section('title', $title)

<x-moonshine::title class="{{ isset($subTitle) && $subTitle ?: 'mb-6' }}">
    {{ $title }}
</x-moonshine::title>

@if($subTitle ?? false)
    <div class="pt-2 mb-6">{!! $subTitle !!}</div>
@endif
