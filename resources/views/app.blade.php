<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('moonshine::title') }}</title>

    @vite(['resources/js/main.ts'], 'vendor/moonshine')
</head>
<body class="relative" id="moonshine"></body>
</html>
