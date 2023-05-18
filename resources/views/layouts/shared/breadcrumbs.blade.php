<x-moonshine::breadcrumbs
    :items="collect($items)
        ->prepend(':::heroicons.outline.home', route('moonshine.index'))
        ->toArray()"
/>
