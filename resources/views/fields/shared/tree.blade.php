<div x-data="tree(@json($element->toValue()->modelKeys()))">
    {!! $element->toTreeHtml() !!}
</div>
