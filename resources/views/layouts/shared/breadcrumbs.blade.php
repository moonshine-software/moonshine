<x-moonshine::breadcrumbs
    :items="collect($items)
        ->prepend(':::heroicons.outline.home', moonshineRouter()->home())
        ->toArray()"
/>
