<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IssueFlow') · IssueFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="site-header">
    <a class="brand" href="{{ Route::has('projects.index') ? route('projects.index') : url('/') }}"><span>IF</span> IssueFlow</a>
    @auth
    <nav>
        @if(Route::has('projects.index'))
            <a class="{{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Projects</a>
        @endif
        @if(Route::has('issues.index'))
            <a class="{{ request()->routeIs('issues.*') ? 'active' : '' }}" href="{{ route('issues.index') }}">Issues</a>
        @endif
        @if(Route::has('tags.index'))
            <a class="{{ request()->routeIs('tags.*') ? 'active' : '' }}" href="{{ route('tags.index') }}">Tags</a>
        @endif
    </nav>
    @endauth
    <div class="account">
        @if(auth()->check())
            <span>{{ auth()->user()->name }}</span>
            @if(Route::has('logout'))
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="link-button">Sign out</button></form>
            @endif
        @else
            @if(Route::has('login'))
                <a class="link-button" href="{{ route('login') }}">Sign in</a>
            @endif
        @endif
    </div>
</header>
<main class="container">
    @if (session('success')) <div class="flash">{{ session('success') }}</div> @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
