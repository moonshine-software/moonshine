@extends("moonshine::layouts.app")

@section('title', config("moonshine.title"))

@section('content')
    <div class="py-12 bg-white bg-white dark:bg-black  shadow-md rounded mb-4">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h3 class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-black dark:text-white sm:text-4xl sm:leading-10">
                    {{ config("moonshine.title") }}
                </h3>
            </div>
        </div>
    </div>
@endsection