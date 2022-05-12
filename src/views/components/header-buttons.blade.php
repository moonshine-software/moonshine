@foreach($data as $btn)
    <a class="ml-2 bg-purple hover:bg-purple text-white font-semibold hover:text-white py-1 px-2 border border-purple hover:border-transparent rounded" href="{{ $btn["href"] }}">{{ $btn["title"] }}</a>
@endforeach