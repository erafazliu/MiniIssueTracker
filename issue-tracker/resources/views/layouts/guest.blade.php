<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; }
        .text-red-700 { color: #b91c1c; }
        .bg-red-100 { background-color: #fee2e2; }
        .border { border-width: 1px; }
        .rounded { border-radius: .375rem; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1); }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
