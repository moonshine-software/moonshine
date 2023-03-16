@props([
    'icon' => '',
    'size' => 5,
    'color' => 'white',
    'class' => ''
])

@includeWhen($icon, "moonshine::ui.icons.$icon", [
    'size' => $size,
    'color' => $color,
    'class' => $class
])
