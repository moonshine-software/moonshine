<div x-data="tree(@json($element->value()->modelKeys()))">
    {!! $element->buildTreeHtml($item) !!}
</div>
