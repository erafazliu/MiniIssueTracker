@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">WELCOME</p>
        <h1>Dashboard</h1>
    </div>
</div>

<div class="card-grid">
    <a href="{{ route('projects.index') }}" class="card" style="text-decoration: none;">
        <div class="eyebrow">MANAGE</div>
        <h2 style="margin-top: 8px;">Projects</h2>
        <p style="color: var(--muted);">Create and view all your projects</p>
    </a>

    <a href="{{ route('issues.index') }}" class="card" style="text-decoration: none;">
        <div class="eyebrow">TRACK</div>
        <h2 style="margin-top: 8px;">Issues</h2>
        <p style="color: var(--muted);">See all issues across your projects</p>
    </a>

    <a href="{{ route('tags.index') }}" class="card" style="text-decoration: none;">
        <div class="eyebrow">ORGANIZE</div>
        <h2 style="margin-top: 8px;">Tags</h2>
        <p style="color: var(--muted);">Create and manage tags for issues</p>
    </a>
</div>

<div class="panel" style="max-width: 420px;">
    <div class="panel-heading">
        <h2>Account</h2>
    </div>
    <p style="padding: 20px; margin: 0;">
        Logged in as <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong>
    </p>
    <form method="POST" action="{{ route('logout') }}" style="padding: 0 20px 20px;">
        @csrf
        <button type="submit" class="button secondary" style="width: 100%;">Sign out</button>
    </form>
</div>
@endsection
