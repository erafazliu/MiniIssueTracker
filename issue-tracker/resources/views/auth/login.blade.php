@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold mb-6 text-center">Login</h1>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded p-3">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label class="block mb-2 text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full mb-4 px-3 py-2 border rounded" />

            <label class="block mb-2 text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full mb-4 px-3 py-2 border rounded" />

            <div class="flex items-center justify-between mb-4">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" value="1" class="form-checkbox" />
                    Remember me
                </label>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Login</button>
        </form>
    </div>
</div>
@endsection
