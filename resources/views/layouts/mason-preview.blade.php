<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mason Preview</title>
    @masonStyles
    @vite(['resources/css/app.css'])
    <style>
        body { margin: 0; padding: 0; }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">
    @include('mason::iframe-preview-content', ['blocks' => $blocks])
</body>
</html>
