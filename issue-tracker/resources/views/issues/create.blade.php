@extends('layouts.app')

@section('title', 'New Issue')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">New Issue</p>
        <h1>Create a new issue</h1>
    </div>
</div>

<form method="POST" action="{{ route('issues.store') }}" class="panel form-panel">
    @csrf
    @include('issues._form', ['submit' => 'Create issue'])
</form>
@endsection
