@props([
    'icon' => '',
    'size' => 5,
    'color',
    'class' => $attributes->get('class')
])

@includeWhen($icon, "moonshine::ui.icons.$icon", array_merge([
    'size' => $size,
    'class' => $class,
    'color' => $color
]))
