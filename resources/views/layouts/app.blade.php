<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Billing Warnet')</title>
    @vite(['resources/css/warnet.css', 'resources/js/warnet.js'])
</head>
<body>
    @yield('content')
    @includeIf('partials.modal')
</body>
</html>