@extends('layouts.guest')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-2xl bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-3xl font-semibold mb-4">Dashboard</h1>
        <p class="mb-6">Welcome, {{ auth()->user()->name ?? auth()->user()->email }}.</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Logout</button>
        </form>
    </div>
</div>
@endsection
