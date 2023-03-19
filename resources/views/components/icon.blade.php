@props([
    'icon' => '',
    'size' => 5,
    'color',
    'class' => ''
])

@includeWhen($icon, "moonshine::ui.icons.$icon", array_merge([
    'size' => $size,
    'class' => $class
], isset($color) ? ['color' => $color] : []))