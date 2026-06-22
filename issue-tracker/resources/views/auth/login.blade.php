@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50">
    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl ring-1 ring-slate-200">
        <h1 class="text-3xl font-semibold mb-6 text-center">Login</h1>

        @include('partials.errors')

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />

            <label class="block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" />

            <div class="flex items-center justify-between text-sm text-slate-600">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                    Remember me
                </label>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Login</button>
        </form>
    </div>
</div>
@endsection
