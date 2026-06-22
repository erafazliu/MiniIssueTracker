@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50">
    <div class="w-full max-w-2xl rounded-3xl bg-white p-10 shadow-xl ring-1 ring-slate-200">
        <h1 class="text-4xl font-semibold mb-4">Dashboard</h1>
        <p class="mb-6 text-slate-700">Welcome, {{ auth()->user()->name ?? auth()->user()->email }}.</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Logout</button>
        </form>
    </div>
</div>
@endsection
