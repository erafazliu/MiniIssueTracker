@extends('layouts.app')

@section('title', 'Edit Issue')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Edit Issue</p>
        <h1>Edit {{ $issue->title }}</h1>
    </div>
</div>

<form method="POST" action="{{ route('issues.update', $issue) }}" class="panel form-panel">
    @csrf
    @method('PUT')
    @include('issues._form', ['submit' => 'Update issue'])
</form>
@endsection
