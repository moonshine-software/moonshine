@if($tabs)
    <ul class="flex cursor-pointer">
        @foreach($tabs as $tab)
            <li class="py-2 px-6 rounded-t-lg text-gray-500 @if((!request()->has("editTab") && $tab["active"]) || $tab["id"] == request("editTab")) bg-white @else bg-gray-200 @endif">
                <a href="{{ url()->current() }}?editTab={{ $tab["id"] }}{{ isset($tab["query"]) && !empty($tab["query"]) ? "&" . http_build_query($tab["query"]) : "" }}">
                    {{ $tab["name"] }}
                </a>
            </li>
        @endforeach
    </ul>
@endif