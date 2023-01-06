<div class="bg-white dark:bg-darkblue shadow-md rounded-lg mb-4 text-white">
    <x-moonshine::resource-renderable
        :components="$decoration->fields()"
        :item="$item"
        :resource="$resource"
    />
</div>
