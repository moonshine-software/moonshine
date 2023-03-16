@section('title', $title)

<h1 class="mb-6 truncate text-md font-medium">{{ $title }}</h1>

@if($subTitle ?? false)
    <div class="pt-2">{!! $subTitle !!}</div>
@endif
