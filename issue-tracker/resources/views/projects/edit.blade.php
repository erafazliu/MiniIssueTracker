@extends('layouts.app')

@section('title','Edit Project')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Edit Project</p>
        <h1>Edit {{ $project->name }}</h1>
    </div>
</div>

<form method="POST" action="{{ route('projects.update', $project) }}">
    @csrf
    @method('PUT')
    @include('projects._form', ['submit' => 'Save changes'])
</form>
@endsection
