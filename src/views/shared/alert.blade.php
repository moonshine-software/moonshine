@if(session()->has('alert'))
    <div x-init="setTimeout(function() {$refs.alert.remove()}, 2000)" x-data="" x-ref="alert"
         class="bg-white dark:bg-black border-t-4 border-purple rounded-b
         text-purple dark:text-white px-4 py-3 shadow-md
         absolute top-0 right-0 z-50 mt-10 mr-5"
         role="alert"
    >
        <div class="flex">
            <div class="py-1"><svg class="fill-current h-6 w-6 text-purple mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
            <div>
                <p class="font-bold">{{ session()->get('alert') }}</p>
            </div>
        </div>
    </div>
@endif