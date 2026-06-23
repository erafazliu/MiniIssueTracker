@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="page-heading compact">
        <div>
            <p class="eyebrow">SIGN IN</p>
            <h1>Welcome</h1>
        </div>
    </div>

    <div class="panel form-panel" style="max-width: 420px; margin: 0 auto;">
        @include('partials.errors')

        <form method="POST" action="{{ route('login.store') }}" class="stack">
            @csrf

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus />
                @error('email')<p class="field-errors">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Password</label>
                <input type="password" name="password" required />
                @error('password')<p class="field-errors">{{ $message }}</p>@enderror
            </div>

            <div class="check">
                <input type="checkbox" name="remember" value="1" id="remember" />
                <label for="remember" style="margin: 0;">Remember me</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Sign in</button>
            </div>
        </form>

        <div class="demo-note">
            <strong>Demo accounts:</strong><br>
            owner@example.com<br>
            member@example.com<br>
            <br>
            All passwords: <code>password</code>
        </div>
    </div>
</div>
@endsection
