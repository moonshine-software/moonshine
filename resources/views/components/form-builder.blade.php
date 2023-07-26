<x-moonshine::form :attributes="$attributes">
    @foreach($getFields() as $fieldOrDecoration)
        {!! view($fieldOrDecoration->getView(), [
            'element' => $fieldOrDecoration,
            'level' => 0,
        ]) !!}
    @endforeach
</x-moonshine::form>
